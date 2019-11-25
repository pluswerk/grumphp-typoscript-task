# Test documentation

## RepeatingRValueSniff

### Description

### Test cases

* If token is not of type right value no issue is created
* If token value is already in use an issue is created
* If token value is not not already in use no issue is created
* If token value matches one ignore pattern no issue is created
* If token value is class name no issue is created
* If token value is class name and ignoreClassNameValues is configured false an issue is created.
* If token value matches constants pattern no issue is created
* If token value is int expression for boolean no issue is generated with data set "int expression for true"
* If token value is int expression for boolean no issue is generated with data set "int expression for false"

* For each token is checked if its value is already in use
* If an issue was created for a token it is added to the file