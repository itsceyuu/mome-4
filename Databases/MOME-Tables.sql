-- User Table
CREATE TABLE mome.users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) UNIQUE,
  role ENUM('client', 'admin') NOT NULL
);

-- Tansactions Table
CREATE TABLE mome.transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(100),
  amount DECIMAL(15,2),
  type ENUM('income', 'expense'),
  date DATE,
  description TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Wishlist Table
CREATE TABLE mome.wishlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  item_name VARCHAR(100),
  target_amount DECIMAL(15,2),
  description TEXT,
  date_added DATE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- saving_target Table
CREATE TABLE mome.saving_targets (
  id int(11) auto_increment primary key ,
  user_id int(11) NOT NULL,
  target_name varchar(100) DEFAULT NULL,
  target_amount decimal(15,2) DEFAULT NULL,
  current_amount decimal(15,2) DEFAULT 0.00,
  description text DEFAULT NULL,
  deadline date DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- articles Table 
CREATE TABLE mome.articles (
  id INT(11) auto_increment primary key,
  title VARCHAR(255) NOT NULL,
  infoTambahan TEXT DEFAULT NULL,
  published_date DATE DEFAULT NULL,
  content TEXT DEFAULT NULL,
  photo_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
);
