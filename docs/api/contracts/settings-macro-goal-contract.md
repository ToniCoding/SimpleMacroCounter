# MacroSettingsGoalUpdate contract

This document defines the contract for the `MacroSettingsGoalUpdate` endpoint (Request and Response).

## Versioning
v1 - 02/07/2026 - Document created and contract defined.  
v2 - 06/09/2026 - Consolidated Request and Response contracts into a single document.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 28-06-2026 | 06-09-2026 | V1.0 | V1.1 |

## Contract Specification

### Endpoint
`/api/v1/settings`

### Overview
- **HTTP Method:** POST
- **Requires Auth:** Yes
- **Auth Type:** Session + JWT

---

## Request

### Structure
```json
{
  "newCalories": 3500,
  "newProtein": 200,
  "newCarbs": 300,
  "newFats": 90,
  "newFiber": 40
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
