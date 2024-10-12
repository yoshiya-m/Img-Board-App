CREATE TABLE IF NOT EXISTS cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    make VARCHAR(50),
    model VARCHAR(50),
    year INT,
    color VARCHAR(20),
    price FLOAT,
    mileage FLOAT,
    transmission VARCHAR(20),
    engine VARCHAR(20),
    status VARCHAR(10)
);
CREATE TABLE IF NOT EXISTS part (
    id INT PRIMARY KEY AUTO_INCREMENT,
    carID INT,
    name VARCHAR(50),
    description VARCHAR(200),
    price FLOAT,
    quantityInStock INT,
    FOREIGN KEY (carID) REFERENCES cars (id)
);

CREATE TABLE IF NOT EXISTS car_parts(
    carID INT AUTO_INCREMENT,
    partID INT,
    quantity INT,
    PRIMARY KEY (carID, partID),
    FOREIGN KEY (carID) REFERENCES cars(id),
    FOREIGN KEY (partID) REFERENCES part(id)
)

