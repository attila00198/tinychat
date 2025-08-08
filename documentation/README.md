# 3-Service Chat Application Architecture Guide

## 🎯 Service Overview

### 1. **Frontend Service** (PHP/HTML/JavaScript)
- **Purpose**: User interface, authentication, basic user management
- **Tech**: Your existing PHP application with JavaScript
- **Port**: 80/443 (web server)

### 2. **API Service** (FastAPI/Python) 
- **Purpose**: User data, authentication, message storage
- **Tech**: FastAPI with JWT authentication
- **Port**: 8000

### 3. **WebSocket Chat Server** (Node.js/Python)
- **Purpose**: Real-time messaging, chat rooms, online presence
- **Tech**: WebSocket server (Socket.io for Node.js or FastAPI WebSockets)
- **Port**: 3000 or 8001

## 🔄 Communication Flow

### Authentication Flow
1. User logs in through **Frontend**
2. **Frontend** → **API**: POST /login with credentials
3. **API** validates and returns JWT token
4. **Frontend** stores JWT in cookie/localStorage
5. **Frontend** → **WebSocket Server**: Connect with JWT token
6. **WebSocket Server** → **API**: Validate JWT token
7. **API** returns user info if token is valid
8. User is now authenticated in chat

### Chat Message Flow
1. User types message in **Frontend**
2. **Frontend** → **WebSocket Server**: Send message via WebSocket
3. **WebSocket Server** → **API**: Store message in database
4. **WebSocket Server** broadcasts message to all connected users
5. **Frontend** receives and displays message in real-time

## 🚀 Implementation Details

### Frontend Service (Your existing PHP app)

**Key responsibilities:**
- Login/Register pages
- Chat UI
- User profile management
- Static file serving

**New additions needed:**
```javascript
// WebSocket connection for chat
const chatSocket = new WebSocket('ws://localhost:3000');
const token = getCookie('access_token');

chatSocket.onopen = function() {
    // Send authentication
    chatSocket.send(JSON.stringify({
        type: 'auth',
        token: token
    }));
};

chatSocket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    displayMessage(data);
};
```

### API Service (FastAPI)

**Key endpoints:**
```python
# Authentication
POST /login
POST /register
GET /users/me

# User management
GET /users/{user_id}
PUT /users/me

# Message storage (called by WebSocket server)
POST /messages
GET /messages?room_id=1&limit=50

# WebSocket server validation
POST /validate-token
```

**Example FastAPI endpoint for WebSocket validation:**
```python
@app.post("/validate-token")
async def validate_token(token_data: dict):
    try:
        payload = jwt.decode(token_data["token"], SECRET_KEY, algorithms=[ALGORITHM])
        user = get_user_by_id(payload.get("sub"))
        return {"valid": True, "user": user}
    except JWTError:
        return {"valid": False}
```

### WebSocket Chat Server

**Key responsibilities:**
- Handle WebSocket connections
- Authenticate users via API
- Broadcast messages
- Manage chat rooms
- Store messages via API

**Example Node.js implementation:**
```javascript
const io = require('socket.io')(3000, {
    cors: { origin: "http://localhost" }
});

io.on('connection', async (socket) => {
    socket.on('auth', async (data) => {
        // Validate token with API
        const response = await fetch('http://localhost:8000/validate-token', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token: data.token })
        });
        
        const result = await response.json();
        
        if (result.valid) {
            socket.user = result.user;
            socket.join('general'); // Join default room
            socket.emit('auth-success', { user: result.user });
        } else {
            socket.emit('auth-error', { message: 'Invalid token' });
        }
    });

    socket.on('message', async (data) => {
        if (!socket.user) return;

        const message = {
            user_id: socket.user.id,
            username: socket.user.username,
            content: data.content,
            timestamp: new Date(),
            room_id: data.room_id || 'general'
        };

        // Store in database via API
        await fetch('http://localhost:8000/messages', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(message)
        });

        // Broadcast to room
        socket.to(message.room_id).emit('message', message);
        socket.emit('message', message); // Send back to sender
    });
});
```

## 🔧 Configuration & Setup

### Docker Compose (Recommended)
```yaml
version: '3.8'
services:
  frontend:
    build: ./web
    ports:
      - "80:80"
    depends_on:
      - api

  api:
    build: ./api
    ports:
      - "8000:8000"
    environment:
      - DATABASE_URL=postgresql://user:pass@db:5432/chatdb
    depends_on:
      - db

  websocket:
    build: ./websocket
    ports:
      - "3000:3000"
    environment:
      - API_URL=http://api:8000

  db:
    image: postgres:13
    environment:
      - POSTGRES_DB=chatdb
      - POSTGRES_USER=user
      - POSTGRES_PASSWORD=pass
```

### Environment Variables
```bash
# API Service
JWT_SECRET_KEY=your-secret-key
DATABASE_URL=postgresql://user:pass@localhost:5432/chatdb

# WebSocket Service
API_URL=http://localhost:8000
FRONTEND_URL=http://localhost
```

## 📁 Recommended Directory Structure
```
chat-app/
├── frontend/           # Your existing PHP app
│   ├── web/
│   ├── static/
│   └── Dockerfile
├── api/               # FastAPI service
│   ├── app/
│   ├── requirements.txt
│   └── Dockerfile
├── websocket/         # WebSocket chat server
│   ├── server.js
│   ├── package.json
│   └── Dockerfile
└── docker-compose.yml
```

## 🔐 Security Considerations

1. **CORS Configuration**: Properly configure CORS on both API and WebSocket servers
2. **JWT Validation**: WebSocket server must validate every JWT with the API
3. **Rate Limiting**: Implement rate limiting for messages
4. **Input Validation**: Sanitize all user inputs
5. **HTTPS/WSS**: Use secure connections in production

## 🚦 Getting Started Steps

1. **Start with API**: Extend your existing FastAPI with message endpoints
2. **Create WebSocket Server**: Simple Node.js server with Socket.io
3. **Update Frontend**: Add WebSocket client code to your existing PHP app
4. **Test Authentication Flow**: Ensure JWT validation works between services
5. **Implement Basic Chat**: Start with a single room, expand later
6. **Add Features**: Private messages, multiple rooms, file sharing, etc.

This architecture gives you clean separation of concerns and allows each service to scale independently. The WebSocket server handles real-time communication while your API manages data persistence and authentication.