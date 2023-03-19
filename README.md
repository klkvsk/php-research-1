
This research project is subjected to find out if the following possible
changes to PHP syntax are needed:

1. [Method cascading](https://en.wikipedia.org/wiki/Method_cascading)
2. Precedence of `new` over `->`

---
### 1. Estimating the use of fluent interfaces

Counting all "return ...;" statements and 
the percent of "return $this;" amongst them.

Results: 
```
Total returns: 557664    
This returns:  60304

10.81%
```
---

### 2. Estimating the need of calling a method right after `new`

Besides counting current usage of this syntax, 
we also count potentially convertable statements 
where method call immediately follows assignment, e.g.:
```
$object = new Class();
$object->...;
```
It does not imply that all of them are convertable, but some do.
A more accurate estimate can be done with static analysis tools.

Results:
```
Number of 'new Class' statements: 255634
Number of '(new Class)->' statements: 453
Potentially convertable to '(new Class)->' statements: 21267

Percent of statements used or convertable to this way: 8.50
```

---
## Notes

This project fetches top 1000 composer packages sorted by popularity
and filtered by "library" type. Those packages are not installed, 
only downloaded without the use of composer.
Expect around 500Mb of downloaded data.
