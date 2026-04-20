import sqlite3

conn = sqlite3.connect("atown.db")

cur = conn.cursor()

cur.execute("""CREATE TABLE users(
	username VARCHAR(200),
	password VARCHAR(200)
);""")

cur.execute("""DROP TABLE users;""")

cur.execute("""INSERT INTO users (username, password) VALUES ('admin', 'NAPA12345');""")

def select_all_tasks(conn):
    cur.execute("SELECT * FROM users")

    rows = cur.fetchall()

    for row in rows:
        print(row)
        
select_all_tasks(conn)

conn.commit()

conn.close()

