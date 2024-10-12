CREATE TABLE IF NOT EXISTS User (
    userID INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20),
    email VARCHAR(50),
    password VARCHAR(20),
    email_confirmed_at VARCHAR(20),
    created_at DATETIME,
    updated_at DATETIME
);
ALTER TABLE User
    DROP COLUMN email_confirmed_at,
    ADD subscription VARCHAR(20),
    ADD subscription_status VARCHAR(20),
    ADD subscriptionCreatedAt DATETIME,
    ADD subscriptionEndsAt DATETIME
;
CREATE TABLE IF NOT EXISTS Post (
    postID INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100),
    content VARCHAR(200),
    created_at DATETIME,
    updated_at DATETIME,
    userID INT,
    FOREIGN KEY (userID) REFERENCES User(userID)
);
CREATE TABLE IF NOT EXISTS PostLike (
    userID INT,
    postID INT,
    PRIMARY KEY (userID, postID),
    FOREIGN KEY (userID) REFERENCES User(userID),
    FOREIGN KEY (postID) REFERENCES Post(postID)
);
CREATE TABLE IF NOT EXISTS Comment (
    commentID INT PRIMARY KEY,
    commentText VARCHAR(200),
    created_at DATETIME,
    updated_at DATETIME,
    userID INT,
    postID INT,
    FOREIGN KEY (userID) REFERENCES User(userID),
    FOREIGN KEY (postID) REFERENCES Post(postID)
);
CREATE TABLE IF NOT EXISTS CommentLike (
    userID INT,
    commentID INT,
    PRIMARY KEY (userID, commentID),
    FOREIGN KEY (userID) REFERENCES User(userID),
    FOREIGN KEY (commentID) REFERENCES Comment(commentID)
);
CREATE TABLE IF NOT EXISTS UserSetting (
    entryID INT PRIMARY KEY,
    userID INT,
    metaKey VARCHAR(20),
    metaValue VARCHAR(20),
    FOREIGN KEY(userID) REFERENCES User(userID)
);
CREATE TABLE IF NOT EXISTS Category (
    categoryID INT PRIMARY KEY,
    categoryName VARCHAR(20)
);
CREATE TABLE IF NOT EXISTS Tag (
    tagID INT PRIMARY KEY,
    tagName VARCHAR(20)
);
CREATE TABLE IF NOT EXISTS PostTag (
    postID INT,
    tagID INT,
    PRIMARY KEY(postID, tagID),
    FOREIGN KEY(postID) REFERENCES Post(postID),
    FOREIGN KEY(tagID) REFERENCES Tag(tagID)
)
