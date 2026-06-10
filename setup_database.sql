CREATE TABLE IF NOT EXISTS users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  username VARCHAR(255) NOT NULL UNIQUE,
  email    VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role     ENUM('member','moderator','president') NOT NULL DEFAULT 'member',
  created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Discussions (idea pitches)
CREATE TABLE IF NOT EXISTS discussions (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  user_id  INT NOT NULL,
  title    VARCHAR(255) NOT NULL,
  body     TEXT NOT NULL,
  created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Replies on discussions
CREATE TABLE IF NOT EXISTS replies (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  discussion_id  INT NOT NULL,
  user_id        INT NOT NULL,
  body           TEXT NOT NULL,
  created        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)       REFERENCES users(id) ON DELETE CASCADE
);

UPDATE users SET role = 'president' WHERE username = 'ullas';
UPDATE users SET role = 'moderator' WHERE username = 'shontu';
