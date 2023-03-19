
This research project is subjected to find out if the following possible
changes to PHP syntax are needed:

1. **[Method cascading](https://en.wikipedia.org/wiki/Method_cascading)**  
A better alternative to fluent interfaces, which are considered [problematic](https://ocramius.github.io/blog/fluent-interfaces-are-evil/)  
Proposed:  
```
$builder
  -->setA()
  -->setB()
  -->set...()
  ->build();
```

2. **Precedence of `new` over `->`**  
Currently required parentheses in `(new Foo())->get()` are superfluous.  
Proposed:  
```
$foo = new Foo()->get();
```

Research is done on top 1000 composer packages.

---
### 1. Estimating the use of fluent interfaces

Counting all "return ...;" statements and 
the percent of "return $this;" amongst them.

Results: 
```
Total returns:     555914
$this returns:      60295

Percent of $this returned of all returns: 10.85 %
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
Number of 'new Class' statements: 199711
Number of '(new Class)->' statements: 4154
Potentially convertable to '(new Class)->' statements: 21610

Percent of statements with call-after-new: 2.08%
Percent of statements possibly convertable to call-after-new: 10.82%
```

---
## Notes

This project fetches top 1000 composer packages sorted by popularity
and filtered by "library" type. Packages are not installed, 
only downloaded without the use of composer.
Expect around 500Mb of downloaded data.

Only *.php files are checked. Code comments and string contents are excluded.
