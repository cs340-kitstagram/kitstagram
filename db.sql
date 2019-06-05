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
    filename VARCHAR(255) NOT NULL,
    caption TEXT,
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

-- password=test
-- Cats Table Initial Values --
INSERT INTO Cats VALUES (1, 'zelda', '$2y$10$OIWoxot8isgsxegW2.m/UepN3La0xUhRu/7RmeI034kE8axH.PqXi', 'Zelda', 'add me');
INSERT INTO Cats VALUES (2, 'link', '$2y$10$RZAUfpa/gazs7yyDmbQtrOusmvvQaeFPsAH2fGWSfDB73K.kw2btO', 'Link', 'I follow back');
INSERT INTO Cats VALUES (3, 'buster', '$2y$10$.AdP88ICsVj8w1IGszHRnuVk//QWP7btvH37xRD6ofz0/W.IK3jEC', 'Buster', 'A cool cat');
INSERT INTO Cats VALUES (5, 'misty', '$2y$10$c1O9OvOSvLxmN1zQoCBFgOsbWTeOxzMsUuBSnT79W8gC7IxjvwtW.', 'Misty', 'I dont know how to use this');
INSERT INTO Cats VALUES (6, 'whisky', '$2y$10$Otd/TL4FMeS4AxNTeF/UoevjLCFvzf9udrnLICdfQPlvYL9OsTrL.', 'Whisky', 'Just another cool cat');
INSERT INTO Cats VALUES (7, 'sirpounce', '$2y$10$NxfO3B7nPQ9IOyFOigVg/uqTrGm3pipCn5ftHFN5ce5Rwb7vK4h9C', 'Sir Pounce', ':)');
INSERT INTO Cats VALUES (10, 'tom', '$2y$10$J/zofpDWcIUzPCqlPlZZcOmxhvIsbaUOKJ5/RP8RTUv1kBELUAqZm', 'Tom', 'Im crazy for catnip');
INSERT INTO Cats VALUES (12, 'arya', '$2y$10$C7ZQchwZIBNJc.ZVr0wSBe/70mnHDvC8u2v9KU1qQxs/7t3MRezhm', 'Arya', '...');
INSERT INTO Cats VALUES (23, 'lulu', '$2y$10$lKO2NWhJTUsjMl3TWvXEp.50uX867BZelPvvcMiKtk3E713MFJ.I.', 'Lulu', 'Lets be friends');
INSERT INTO Cats VALUES (33, 'poppy', '$2y$10$0Inka0ZnIR.21LjYmGOA8e14LtCR5U/Ke7HjzexK5mFToNM4BC8Y6', 'Poppy', 'Im Poppy');
-- Friends Table Initial Values --
INSERT INTO Friends VALUES (1, 2);
INSERT INTO Friends VALUES (1, 5);
INSERT INTO Friends VALUES (1, 6);
INSERT INTO Friends VALUES (1, 10);
INSERT INTO Friends VALUES (3, 10);
INSERT INTO Friends VALUES (3, 6);
INSERT INTO Friends VALUES (7, 12);
INSERT INTO Friends VALUES (12, 23);
INSERT INTO Friends VALUES (5, 23);
INSERT INTO Friends VALUES (23, 33);
-- Selfies Table Initial Values --
INSERT INTO Selfies VALUES (101, 'selfie link', 'me', current_timestamp(), 1, 2);
INSERT INTO Selfies VALUES (201, 'selfie link2', 'selfie 1', current_timestamp(), 2, 0);
INSERT INTO Selfies VALUES (202, 'selfie link3', 'selfie 2', current_timestamp(), 2, 2);
INSERT INTO Selfies VALUES (301, 'selfie link4', 'happy cat', current_timestamp(), 3, 0);
INSERT INTO Selfies VALUES (501, 'selfie link5', 'misty selfie', current_timestamp(), 5, 2);
INSERT INTO Selfies VALUES (601, 'selfie link6', 'whisky selfie', current_timestamp(), 6, 1);
INSERT INTO Selfies VALUES (701, 'selfie link7', 'pouncin', current_timestamp(), 7, 0);
INSERT INTO Selfies VALUES (1001, 'selfie link8', 'tom selfie', current_timestamp(), 10, 1);
INSERT INTO Selfies VALUES (1201, 'selfie link9', 'arya selfie', current_timestamp(), 12, 2);
INSERT INTO Selfies VALUES (2301, 'selfie link0', 'lulu selfie', current_timestamp(), 23, 0);
-- Likes Table Initial Values --
INSERT INTO Likes VALUES (1, 101);
INSERT INTO Likes VALUES (2, 101);
INSERT INTO Likes VALUES (1, 202);
INSERT INTO Likes VALUES (3, 501);
INSERT INTO Likes VALUES (6, 202);
INSERT INTO Likes VALUES (7, 1001);
INSERT INTO Likes VALUES (7, 1201);
INSERT INTO Likes VALUES (12, 501);
INSERT INTO Likes VALUES (23, 1201);
INSERT INTO Likes VALUES (33, 601);
-- Comments Table Initial Values --
INSERT INTO Comments VALUES (202, 1, 1, current_timestamp(), 'cool pic');
INSERT INTO Comments VALUES (101, 1, 3, current_timestamp(), 'purrfect');
INSERT INTO Comments VALUES (101, 2, 6, current_timestamp(), '<3');
INSERT INTO Comments VALUES (101, 3, 12, current_timestamp(), 'nice');
INSERT INTO Comments VALUES (1001, 1, 7, current_timestamp(), 'meowzers');
INSERT INTO Comments VALUES (701, 1, 12, current_timestamp(), 'thats my friend');
INSERT INTO Comments VALUES (501, 1, 1, current_timestamp(), 'aww');
INSERT INTO Comments VALUES (2301, 1, 33, current_timestamp(), 'cutie');
INSERT INTO Comments VALUES (601, 1, 23, current_timestamp(), 'wow');
INSERT INTO Comments VALUES (202, 2, 12, current_timestamp(), 'your best selfie yet!');