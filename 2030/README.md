# Simple Stock Market Project

A beginner-friendly stock market tracking application built with HTML, CSS, JavaScript, jQuery, PHP, and MySQL.

## Features

- View a list of popular stocks with their current prices and price changes
- Add new stocks to the database
- Update existing stock prices and change percentages
- Auto-refresh stock data every 30 seconds

## Setup Instructions

### Prerequisites

- XAMPP, WAMP, MAMP, or any PHP/MySQL server environment
- Web browser

### Installation

1. Clone or download this repository to your local web server directory (e.g., `htdocs` for XAMPP)
2. Start your Apache and MySQL services
3. Open your web browser and navigate to `http://localhost/phpmyadmin`
4. Create a new database named `stock_market` (if not using the automated setup)
5. Open your web browser and navigate to `http://localhost/your-project-folder/db_setup.php` to set up the database and sample data
6. After successful database setup, navigate to `http://localhost/your-project-folder/index.php`

## Usage

### Viewing Stocks

The main page displays a table of all stocks in the database with their symbols, names, current prices, change percentages, and last updated timestamps.

### Adding New Stocks

1. Fill out the "Add New Stock" form
2. Enter a valid stock symbol (1-5 uppercase letters)
3. Enter the company name
4. Enter the current price (must be greater than 0)
5. Enter the change percentage (can be positive or negative)
6. Click "Add Stock"

### Updating Stock Prices

1. Select a stock from the dropdown menu in the "Update Stock Price" form
2. Enter the new price (must be greater than 0)
3. Enter the new change percentage
4. Click "Update Stock"

## Project Structure

- `index.php` - Main application page
- `config.php` - Database connection configuration
- `db_setup.php` - Database and sample data setup
- `add_stock.php` - Handles adding new stocks
- `update_stock.php` - Handles updating existing stocks
- `refresh_stocks.php` - AJAX endpoint for refreshing stock data
- `style.css` - CSS styles for the application

## For Beginners

This project demonstrates:

- Basic PHP database operations (CRUD)
- Simple form handling and validation
- Using jQuery for frontend interactions
- Responsive web design with CSS
- Real-time data updates with AJAX

Feel free to modify and extend this project as you learn more about web development!
