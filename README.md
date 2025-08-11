Follow these steps to run the project locally.

1.  Clone the Repository
https://github.com/Joenll/AND-I-QUOTE.git
cd <repo-name>

2.  Install Dependencies for both Backend and Frontend
# Install Laravel (backend) dependencies
composer install

# Install Node.js dependencies for frontend assets
npm install


3.Configure Environment - Copy .env.example to .env
cp .env.example .env 
// Set your database, mail, and app configurations in .env.

Example MySQL section:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=and_i_quote
DB_USERNAME=root
DB_PASSWORD=

Example Brevo mail section:
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-email@example.com
MAIL_PASSWORD=your-brevo-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="And I Quote"


4. Run Migrations
php artisan migrate


5.Run the Development Servers 
# different terminal

# for development
php artisan serve
npm run dev

# for production
npm run build
php artisan serve

Now open http://127.0.0.1:8000 in your browser.


6.API Endpoints
-All API responses are JSON format.
Base URL: http://127.0.0.1:8000/api/v1

# Customers

| Method | Endpoint                | Description                                 |
| ------ | ----------------------- | ------------------------------------------- |
| GET    | `/customers/search`     | Search customers by name, email, or contact |
| GET    | `/customers`            | List all customers                          |
| GET    | `/customers/{customer}` | Get a specific customer by ID               |
| POST   | `/customers`            | Create a new customer                       |
| PUT    | `/customers/{customer}` | Update customer details                     |
| DELETE | `/customers/{customer}` | Delete a customer (and their quotations)    |


7.Sample Requests


# Example Create Customer:\
POST /api/v1/customers
Content-Type: application/json

{
  "name": "John Doe",
  "dob": "1990-05-12",
  "address": "123 Street Name",
  "email": "john@example.com",
  "contact": "09123456789"
}



# Quotations
| Method | Endpoint                  | Description                |
| ------ | ------------------------- | -------------------------- |
| GET    | `/quotations`             | List all quotations        |
| GET    | `/quotations/{quotation}` | Get details of a quotation |
| POST   | `/quotations`             | Create a new quotation     |
| PUT    | `/quotations/{quotation}` | Update a quotation         |
| DELETE | `/quotations/{quotation}` | Delete a quotation         |


# Email-Sending
| Method | Endpoint                             | Description                              |
| ------ | ------------------------------------ | ---------------------------------------- |
| POST   | `/quotations/{quotation}/send-email` | Send a quotation to the customer's email |

# Quotation Items
| Method | Endpoint                | Description                    |
| ------ | ----------------------- | ------------------------------ |
| GET    | `/quotation-items`      | List all quotation items       |
| GET    | `/quotation-items/{id}` | Get details of a specific item |
| PUT    | `/quotation-items/{id}` | Update a quotation item        |
| DELETE | `/quotation-items/{id}` | Delete a quotation item        |



 8. Email Sending Notes
# The email will be sent using the Brevo API.

# Ensure your .env mail settings are correct before testing.
POST /api/v1/quotations/{quotation}/send-email



Brevo API Setup
# Step 1 — Create a Brevo Account
1. Go to https://app.brevo.com and sign up for a free account.
2. Verify your email address and complete the account setup.

# Step 2 — Verify a Sender Email
Brevo only allows sending from verified addresses.

1. Go to Senders & IP → Senders in your Brevo dashboard.
2. Click Add a New Sender.
3. Enter the From Name (e.g., AND I QUOTE) and From Email (e.g., yourname@example.com).
4. Brevo will send you a confirmation email — click the link inside to verify.

# Step 3 — Generate API Key
In the Brevo dashboard, go to SMTP & API → API Keys.

1. Click Generate a New API Key.
2. Choose a name for your key (e.g., Quotation App) and save it.
3. Copy the generated API key — you’ll use it in .env.

# example .env mail config (Brevo API)
BREVO_API_KEY=your-brevo-api-key
MAIL_FROM_ADDRESS=verified-sender@example.com
MAIL_FROM_NAME="AND I QUOTE"


