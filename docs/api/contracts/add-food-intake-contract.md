# AddFood contract

This document defines the contract for the `AddFood` endpoint.

## Versioning
v1 - 01/07/2026 - Document created and contract defined.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 28-06-2026 | 10-09-2026 | V1.0 | V1.1 |

## Contract Specification

### Endpoint
`/api/v1/add-food`

### Overview
- **HTTP Method:** POST
- **Requires Auth:** Yes
- **Auth Type:** Session + JWT

---

## Request

### Structure
```json
{
  "id": 10,
  "grams": 100
}
```

## Response

### Structures

#### On HTTP 200 OK
```JSON
{
  "success": true,
  "redirect_url": "/"
}
```

#### On HTTP 500 INTERNAL SERVER ERROR OR ANY ERROR (TO BE IMPROVED)
```JSON
{
  "success": false,
  "redirect_url": "/addFood"
}
```

