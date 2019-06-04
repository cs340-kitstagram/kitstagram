DROP TRIGGER IF EXISTS incr_likes;
DROP TRIGGER IF EXISTS decr_likes;

DROP TABLE IF EXISTS Friends;
DROP TABLE IF EXISTS Likes;
DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Selfies;
DROP TABLE IF EXISTS Cats;

CREATE TABLE Cats (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(80) NOT NULL,
    name VARCHAR(50) NOT NULL,
    profile TEXT
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Friends (
    cat_id INTEGER NOT NULL,
    friend_id INTEGER NOT NULL,
    FOREIGN KEY(cat_id) REFERENCES Cats (id),
    FOREIGN KEY(friend_id) REFERENCES Cats (id),
    PRIMARY KEY(cat_id, friend_id)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Selfies (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    date_uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cat_id INTEGER NOT NULL REFERENCES Cats(id),
    likes INTEGER DEFAULT 0, -- updated by trigger on Likes
    FOREIGN KEY(cat_id) REFERENCES Cats(id)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Likes (
    cat_id INTEGER NOT NULL,
    selfie_id INTEGER NOT NULL,
    FOREIGN KEY(cat_id) REFERENCES Cats(id),
    FOREIGN KEY(selfie_id) REFERENCES Selfies(id),
    PRIMARY KEY(cat_id, selfie_id)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TABLE Comments (
    selfie_id INTEGER NOT NULL REFERENCES Selfies(id),
    comment_number INTEGER NOT NULL,
    cat_id INTEGER NOT NULL REFERENCES Cats(id),
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    body TEXT,
    FOREIGN KEY(selfie_id) REFERENCES Selfies(id),
    FOREIGN KEY(cat_id) REFERENCES Cats(id),
    PRIMARY KEY(selfie_id, comment_number)
) ENGINE=InnoDB COLLATE utf8mb4_general_ci;

CREATE TRIGGER incr_likes
    AFTER INSERT
    ON Likes FOR EACH ROW
    UPDATE Selfies SET likes = likes + 1 WHERE id = new.selfie_id;

CREATE TRIGGER decr_likes
    AFTER DELETE ON Likes
    FOR EACH ROW
    UPDATE Selfies SET likes = likes - 1 WHERE id = old.selfie_id;
