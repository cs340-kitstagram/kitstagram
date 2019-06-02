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
) ENGINE=InnoDB;

CREATE TABLE Friends (
    friender_id INTEGER NOT NULL REFERENCES Cats (id),
    friendee_id INTEGER NOT NULL REFERENCES Cats (id),
    PRIMARY KEY(friender_id, friendee_id)
) ENGINE=InnoDB;

CREATE TABLE Selfies (
    id INTEGER PRIMARY KEY AUTO_INCREMENT
    date_uploaded TIMESTAMP NOT NULL,
    cat_id INTEGER NOT NULL REFERENCES Cats(id),
) ENGINE=InnoDB;

CREATE TABLE Likes (
    cat_id INTEGER REFERENCES Cats(id),
    selfie_id INTEGER REFERENCES Selfies(id),
);

CREATE TABLE Comments (
    selfie_id INTEGER REFERENCES Selfies(id),
    comment_number INTEGER NOT NULL,
    cat_id INTEGER NOT NULL REFERENCES Cats(id),
    date_posted TIMESTAMP NOT NULL,
    body TEXT,
    PRIMARY KEY(selfie_id, comment_number)
)
