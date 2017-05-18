# Api Filtering Specification 1

This section of the standard explain what should be considered as the standard for HTTP parameters that can be used on API to ensure a coherence between API requests/responses and third party codes.

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be interpreted as described in [RFC 2119](http://www.ietf.org/rfc/rfc2119.txt).

## 1. Where

The `where` parameter MUST be an associative array with table column offsets and MAY have several columns. The values SHOULD be either :

* A valid value for the default `=` operator.
  * Example : `?where[column_1]=value_1`
* Another associative array with operators and associated value.
  * Example : `?where[column_1][>]=35`

### 1.1 Operators

The following operators MUST be implemented : `=`, `<`, `>`, `<=`, `>=`, `<>`, `!=`, `like`, `not like`, `between`, `in`, `not in` and SHOULD have the behaviour of the SQL correspondant operator.

### 1.2 Values

* The `=`, `<`, `>`, `<=`, `>=`, `<>`, `!=`, `like`, `not like` operators MUST have an alphanumeric string as an associated value.
  * Example : `?where[column_1][!=]=value_1`
* The `like` and `not like` operators MAY include wildcard chars defined and SQL specifications
  * Example : `?where[column_1][like]=abc%`
* The `in` and `not in` operator SHOULD have comma separated values.
  * Example : `?where[column_1][in]=value_1,value_2,value_3`
* The `between` operator SHOULD have two comma separated values.
  * Example : `?where[column_1][between]=1,3`

## 2. OrderBy

he `order_by` parameter MUST be an associative array with table column offsets and MAY have several columns. The values SHOULD be either `asc` or `desc`.

Example : `?order_by[column_1]=desc`

## 2. Limit

The `limit` parameter MUST have a valid numeric value and SHOULD have the behaviour of the SQL `LIMIT` instruction.

Example : `?limit=50`

## 2. Offset

The `offset` parameter MUST have a valid numeric value and SHOULD have the behaviour of the SQL `OFFSET` instruction.

Example : `?offset=3`
