# MacroIntakeManualUpdate contract

This document defines the contract for the `MacroIntakeManualUpdate` endpoint.

## Versioning
v1 - 01/07/2026 - Document created and contract defined.  
v2 - 06/09/2026 - Consolidated Request and Response contracts into a single document.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 28-06-2026 | 06-09-2026 | V1.0 | V1.1 |

## Contract Specification

### Endpoint
`/api/v1/modify-macros`

### Overview
- **HTTP Method:** POST
- **Requires Auth:** Yes
- **Auth Type:** Session + JWT

---

## Request

### Structure
```json
{
  "protein": 100.00,
  "carbs": 100.00,
  "fats": 100.00,
  "fiber": 100.00,
  "intent": "add"
}
```

## Response

### Structures

#### On HTTP 200 OK
```JSON
{
  "successMessage": "Successfully updated the macro-nutrient goal."
}
```

#### On HTTP 400 BAD REQUEST
```JSON
{
  "errorMessage": "<ErrorMessage>"
}
```

#### On HTTP 500 INTERNAL SERVER ERROR
```JSON
{
  "errorMessage": "There was an error processing the request for updating the macro-nutrient goal."
}
```
