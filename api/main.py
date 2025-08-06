from fastapi import FastAPI, Depends, HTTPException
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware
import bcrypt
import jwt
from datetime import datetime, timedelta
from pydantic import BaseModel


class LoginReq(BaseModel):
    username: str
    password: str


app = FastAPI()
security = HTTPBearer()

origins = [
    "*"
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,  # vagy ["*"] ha mindenhonnan engednéd
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


SECRET_KEY = "supertitkoskulcs"

dumy_user_db = {
    1: {
        "id": 2,
        "username": "Admin",
        "password": bcrypt.hashpw(b'root123', bcrypt.gensalt()),
        "role": "admin"
    },
    0: {
        "id": 1,
        "username": "Kamakosan",
        "password": bcrypt.hashpw(b'Krassus001', bcrypt.gensalt()),
        "role": "user"
    },
}


def create_jwt(u_name: str) -> str:
    payload = {
        "sub": u_name,
        "exp": datetime.now() + timedelta(minutes=30)
    }
    return jwt.encode(payload, SECRET_KEY, algorithm="HS256")


def verify_jwt(credentials: HTTPAuthorizationCredentials = Depends(security)):
    try:
        payload = jwt.decode(credentials.credentials,
                             SECRET_KEY, algorithms="HS256")
        return payload["sub"]
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token Expired")
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail="Invalid Token")


@app.get("/")
def root():
    return {"message": "Go to the login page. Please."}


@app.post("/login")
def login(credentials: LoginReq):
    username = credentials.username
    password = credentials.password

    for user in dumy_user_db.values():
        if username == user.get("username"):
            hashed = user.get("password")
            if not hashed or not bcrypt.checkpw(password.encode(), hashed):
                raise HTTPException(
                    status_code=401, detail="Invalid Credentials")
            else:
                token = create_jwt(username)
                return {"access_token": token,
                        "id": user.get("id"),
                        "username": user.get("username"),
                        "role": user.get("role")}
    raise HTTPException(
        status_code=401, detail="Invalid Credentials")


@app.get("/protected")
def protected(user: str = Depends(verify_jwt)):
    return {"message": f"Wellcome {user}!",
            "message2": "This route was protected by JWT."}


@app.get("/user")
def user():
    return {
        "message": "Not implemented"
    }
