---
view: layout.post
title: The difference between Traits, Interfaces, and Abstract Classes in PHP
description: These three structures can be confusing to PHP newcomers or experienced devs, so let's go over what each one does and when it's best to use them.
categories: php
published: Aug 07 2022
excerpt: If you've been working with PHP regularly, chances are you've run across an Interface, Trait, or Abstract Class. At first glance, they might appear to have a few similarities between them, and it can be hard to make out their differences and use cases.
---

If you've been working with PHP regularly, chances are you've run across an Interface, Trait, or Abstract Class. At first glance, they might appear to have a few similarities between them, and it can be hard to make out their differences and use cases. By the end of this article, you should be able to easily tell what sets them apart, and when it's best to use one over the other.

## tl;dr

- An **Abstract Class** can contain method signatures *as well as* common methods, but can't be instantiated on its own. Good for creating a common parent to share between classes.
- A **Trait** is a group of properties and methods for *code re-use*, and multiple can be added to a single class. Good for organization and reducing repetition. 
- An **Interface** is a set of method *signatures* to enforce a particular implementation in the class they're added to. Good for adding structure and standardization.

Want to learn more about each of these? **Keep reading!**

## Abstract Class

An abstract class is structured a lot like a normal class, but contains abstract methods as well. Unlike a normal class though, it can't be instantiated by itself. They make good starting points for classes that would likely share a common parent.

Let's say that we had Cat, Dog, and Hamster classes. These might share some common methods and functionality, so a parent abstract class could be created to both *add shared methods* as well as *enforce required implementations* in the child classes.

A parent abstract class for those might look something like this:

```php
<?php
abstract class Pet
{
    abstract protected function greet();

    public function hasFur()
    {
        return true;
    }
}
```

We have an abstract method `greet()` which will force any class extending `Pet` to implement that method. Then we have a public `hasFur()` method which will be accessible to any object created from a class that extends this abstract class.

So now we can create a class for a particular kind of pet:

```php
<?php
class Cat extends Pet
{
    public function greet()
    {
        return 'Meow!';
    }
}

$cat = new Cat();
$cat->greet(); // Meow!
$cat->hasFur(); // true
```

Some takeaways about abstract classes:

- Can't be instantiated on their own
- Abstract methods just declare a signature (no functionality)
- Used by a child class with `extends`
- Act as a sort of partially-built class

## Trait

Traits are a way to re-use code in multiple (and sometimes completely unrelated) classes. Unlike abstract classes, multiple traits can be used on a single class with the `use` statement.

They're regularly reached for to group together somewhat related methods and properties, adding that functionality to classes they're used in.

For example, we might have two traits `HasLegs` and `HasFins`:

```php
<?php
trait HasLegs
{
    public function walk()
    {
        $steps = 0;
        while (true) {
            $steps++;
        }
    }
}
```

```php
<?php
trait HasFins
{
    public function swim()
    {
        $laps = 0;
        while (true) {
            $laps++;
        }
    }
}
```

We're continuing with the animal examples from earlier, so let's say that we had a Cat object that clearly has legs and can walk. We can use our `HasLegs` trait to give our `Cat` class a `walk()` method that can be used:

```php
<?php
class Cat
{
    use HasLegs;
}

$cat = new Cat();
$cat->walk();
```

Or, what if we had a Duck object, that kind of has both legs and fins? We can use both traits and give our class `walk()` and `swim()` methods:

```php
<?php
class Duck
{
    use HasLegs, HasFins;
}

$duck = new Duck();
$duck->walk();
//or
$duck->swim();
```

Some takeaways about traits:

- Perfect for code reuse
- Implemented in the class body with `use`
- Multiple can be used in the same class
- Can have both properties and methods

## Interface

Interfaces are a way to enforce specific implementations in classes that use them. Interfaces can **only** contain method signatures, there's zero functionality in them at all. They're mostly there to ensure structure and act as blueprints for building classes.

Let's take this example of an interface:

```php
<?php
interface Bug
{
    public function legs();
    public function eyes();
}
```

Again, there's no implementation of the `legs()` and `eyes()` methods, just the signature that they're required in classes that include this interface. So, if we create classes that implement our `Bug` interface, those methods need to be available in each of them.

```php
<?php
class Spider implements Bug
{
    public function legs()
    {
        return 8;
    }

    public function eyes()
    {
        return 8;
    }
}
```

```php
<?php
class Beetle implements Bug
{
    public function legs()
    {
        return 6;
    }

    public function eyes()
    {
        return 2;
    }
}
```

So now we have two classes each implementing our `Bug` interface, which are both required to include the `legs()` and `eyes()` methods as well as their own (usually unique) implementation. 

Some takeaways about interfaces:

- Act as blueprints for classes
- Used in the class declaration with `implements`
- Multiple can be used in the same class
- Can only contain method signatures (no properties)

## Wrap Up

Hopefully now you have a better understanding of the distinctions and similarities between Abstract Classes, Traits, and Interfaces. Each of them are powerful and when combined in different ways can add a lot of power and structure to your PHP applications.

If you have any questions about this, or anything else related to web development, feel free to let me know in the comments or reach out to me on [Twitter](https://twitter.com/aschmelyun)!