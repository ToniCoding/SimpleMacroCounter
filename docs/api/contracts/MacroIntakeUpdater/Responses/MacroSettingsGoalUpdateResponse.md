# MacroSettingsGoalUpdateResponse contract.

This document defines the contract for the `MacroSettingsGoalUpdateResponse`.

## Versioning
v1 - 02/07/2026 - Document created and contract defined.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 02/07/2026 | N/A | V1.1 | N/A |

## Contract

### Endpoint
/api/settings

### Structure
The contract should respect the following JSON structure:

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

### Misc
- **Response to:** 
- **Requires auth:** 
- **Auth type:** 

### Field description

In this section we describe the contract fields.

1. successMessage - Success message to notify the user.
2. errorMessage - Error message informing about the error.
