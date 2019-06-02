DROP TABLE IF EXISTS Friends;
DROP TABLE IF EXISTS Likes;
DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Selfies;
DROP TABLE IF EXISTS Cats;

CREATE TABLE Cats (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    hashed_password VARCHAR(80) NOT NULL,
    name VARCHAR(50) NOT NULL,
    profile TEXT
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Friends (
    friender_id INTEGER NOT NULL REFERENCES Cats (id),
    friendee_id INTEGER NOT NULL REFERENCES Cats (id),
    PRIMARY KEY(friender_id, friendee_id)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Selfies (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    date_uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cat_id INTEGER NOT NULL REFERENCES Cats(id)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Likes (
    cat_id INTEGER REFERENCES Cats(id),
    selfie_id INTEGER REFERENCES Selfies(id),
    PRIMARY KEY(cat_id, selfie_id)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Comments (
    selfie_id INTEGER REFERENCES Selfies(id),
    comment_number INTEGER NOT NULL,
    cat_id INTEGER NOT NULL REFERENCES Cats(id),
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    body TEXT,
    PRIMARY KEY(selfie_id, comment_number)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;
