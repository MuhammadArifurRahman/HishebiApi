# Backend Implementation Requirement: Hishebi REST API

## 1. Master Agent Prompt
> **Instruction for AI Agent:**
> "Build a robust Node.js/Express REST API for the 'Hishebi' Personal Accounting application. The API must handle financial transactions, debt management (dues), and user profiles. It should support full CRUD operations, data persistence (via MongoDB or PostgreSQL), and include a text extraction endpoint for processing natural language financial entries. Ensure all responses are JSON-formatted and include proper HTTP status codes."

---

## 2. Technical Specification

### Environment Setup
- **Stack:** Node.js, Express, TypeScript.
- **Middleware:** `cors`, `helmet`, `morgan` (logging), `express.json()`.
- **Authentication:** Bearer Token (JWT) or Simple API Key (as configured in `.env`).

### Endpoint Documentation

#### A. Transactions (`/api/transactions`)
- **GET `/`**: Returns a list of all transactions.
- **POST `/`**: Creates a new transaction.
  - **Payload:** `{ "title": string, "amount": number, "type": "in"|"out", "date": "YYYY-MM-DD" }`
- **PUT `/:id`**: Updates an existing transaction.
- **DELETE `/:id`**: Removes a transaction.

#### B. Dues & Debt (`/api/dues`)
- **GET `/`**: Returns all owed and receivable entries.
- **POST `/`**: Records a new debt/asset.
  - **Payload:** `{ "name": string, "mobile": string, "amount": number, "type": "owe"|"receivable", "reason": string?, "dueDate": string? }`
- **PUT `/:id`**: Updates status or details of a specific due entry.
- **DELETE `/:id`**: Deletes a due entry.

#### C. User Profile (`/api/profile`)
- **GET `/`**: Fetches current user name and email.
- **PUT `/`**: Updates user profile information.

#### D. Intelligent Extraction (`/api/extract`)
- **POST `/`**: Processes raw text to identify financial entities.
  - **Input:** `{ "text": string }`
  - **Output:** `{ "title": string, "amount": number, "type": "in"|"out", "date": string }`

---

## 3. Data Schemas (TypeScript)

```typescript
type TransactionType = 'in' | 'out';
type DueType = 'owe' | 'receivable';

interface Transaction {
  id: string; // Server-generated UUID
  title: string;
  amount: number;
  type: TransactionType;
  date: string; // ISO format
}

interface DueEntry {
  id: string;
  name: string;
  address?: string;
  mobile: string;
  reason?: string;
  amount: number;
  type: DueType;
  dueDate?: string;
  createdAt: string;
}

interface UserProfile {
  name: string;
  email: string;
}
```

---

## 4. Delivery Requirements
1. **Error Handling:** Centralized error middleware to catch 404s and 500s.
2. **Validation:** Use `zod` or `joi` to validate request bodies before processing.
3. **Pagination:** Implement query params `?limit=10&offset=0` for the GET transactions endpoint.
4. **Consistency:** Ensure `cashIn` and `cashOut` sums match the transaction history.


## Need to careful
The database need to use sqlite and need to config via .env so that
server database not replace by local database. cod this way and in the index page also write all the api end point same documentation like api doc so that we can 
read and implement in our application 