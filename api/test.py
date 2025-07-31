import bcrypt

dumy_user_db = {
    0: {
        "id": 1,
        "username": "Kamakosan",
        "password": bcrypt.hashpw(b'Krassus001', bcrypt.gensalt()),
        "role": "user"
    },
    1: {
        "id": 2,
        "username": "admin",
        "password": bcrypt.hashpw(b'root', bcrypt.gensalt()),
        "role": "admin"
    }
}


for user in dumy_user_db.values():
    print(user["username"])
