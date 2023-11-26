---
title: When to use ref vs reactive in Vue
slug: when-to-use-ref-vs-reactive-in-vue
description: The Composition API introduced two different ways of providing reactivity in your Vue app, but it can be a bit confusing when choosing which method you should use.
categories: vue,javascript
published_at: Jul 16 2022
excerpt: The release of Vue 3 introduced two new ways of adding reactivity to data in your components, ref and reactive. There's been a bit of confusion surrounding which one's better, or when either should be used. I'm here to hopefully shed some light on their differences, and show how I use them in my applications.
---

The release of Vue 3 introduced two new ways of adding reactivity to data in your components: `ref()` and `reactive()`. There's been a bit of confusion surrounding which one's better, or when either should be used. I'm here to hopefully shed some light on their differences, and show how _I_ use them in my applications.

## Ref

The `ref()` method takes a single value, and returns back a mutable and reactive object. Let's take a look at this example code:

```js
const amount = ref(0)
```

If we wanted to create a method that incremented the amount up by one, you might be inclined to think we can do something like this:

```js
const increaseAmount = () => {
    amount++
}
```

But with `ref()` you need to use an intermediary property called `value` in order to _retrieve_ or _manipulate_ the data inside of the ref object. So instead, you'd do:

```js
const increaseAmount = () => {
    amount.value++
}
```

The `ref()` method takes any JavaScript primitive, so you can pass in booleans, strings, integers, or objects.

```vue
<script setup>
const active = ref(true)
const message = ref('Hello, world!')
const amount = ref(23)
const user = ref({
    name: 'Andrew',
    email: 'andrew@test.com'
})

user.value.email = 'andrew@example.com'
</script>

<template>
    <h1>{{ message }}</h1> <!-- Hello, world! -->
</template>
```

When referencing or changing a value (outside of a template), you always have to use the `.value` property on the returned object.

## Reactive

The `reactive()` method works similarly to ref, but it **only accepts objects**.

```js
// not reactive
const user = reactive('Andrew')

// reactive
const user = reactive({ name: 'Andrew' })
```

Unlike ref, we don't need to use an intermediary property like `.value` in order to get or change the properties of our reactive object. We can just call the properties of the object directly:

```vue
<script setup>
const user = reactive({ name: 'Andrew' })
user.name = 'Ashley'
</script>

<template>
    <p>My name is {{ user.name }}</p> <!-- My name is Ashley -->
</template>
```

An interesting feature of `reactive()` is that it can also unwrap ref objects for use within itself.

```js
const items = ref(10)
const cart = reactive({ items })

items.value++
console.log(cart.items) // 11

cart.items++
console.log(items.value) // 12
```

The reactivity between the two also remains, so that updating the value of one triggers an update on the value of the other.

## The bottom line

Both `ref()` and `reactive()` add reactivity to your Vue components. They allow you to have data that updates and responds in real-time across your application. The differences boil down to:

- What data you're passing in, and 
- If you want to deal with an intermediary property to get the value

For me personally, I usually stick with `ref()` for reactive attributes in my components. If I start having more than just a few of them though, I create a local "state" object and use `reactive()` instead.

That way instead of this:

```js
const name = ref('Andrew')
const checked = ref(false)
const games = ref(['Factorio', 'CS:GO', 'Cities: Skylines'])
const elem = ref('#active')
```

I have this:

```js
const state = reactive({
    name: 'Andrew',
    checked: false,
    games: ['Factorio', 'CS:GO', 'Cities: Skylines'],
    elem: '#active'
})
```

Well, I hope this made the differences (and similarities) between ref and reactive in Vue a little clearer.

If you have any questions about this, or anything else related to web development, feel free to let me know in the comments or reach out to me on [Twitter](https://twitter.com/aschmelyun)!