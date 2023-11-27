---
title: Why you should be using Vue's new Composition API
slug: why-you-should-be-using-vue-new-composition-api
description: Vue 3 introduced the composition API and while it looks intimidating, here's a few practical examples of components rewritten using this new interface.
categories: vue,javascript
published_at: Aug 26 2021
excerpt: You keep hearing about this composition API in Vue. But it's a little scary and intimidating, and why it's so much better isn't really all that clear to you. In this article you'll see exactly why you should learn to use it by comparing the old way to the new way. The examples also start out simple and get more complex, so you can see that the composition API isn't really all that different from what you're used to.
---

You keep hearing about this [composition API](https://v3.vuejs.org/guide/composition-api-introduction.html) in Vue. But it's a little scary and intimidating, and why it's so much better isn't really all that clear to you.

In this article you'll see exactly why you should learn to use it by comparing the old way to the new way. The examples also start out simple and get more complex, so you can see that the composition API isn't really all that different from what you're used to.

This replaces Vue 2's current options API, but the good news is that you aren't *required* to use it in Vue 3 applications. You can still use the tried-and-true options API and write your components just like you would have previously in Vue 2. For those who want to adopt this new method now or just want to familiarize with the updates, here's a few examples of some common, simple components, re-written using Vue 3's composition API.

## A simple counter

Pretty much the go-to "Hello world" of frontend frameworks, the counter component. Let's see what one looks like in Vue 2:

```vue
<template>
  <div class="counter">
    <span>{{ counter }}</span>
    <button @click="counter += 1">+1</button>
    <button @click="counter -= 1">-1</button>
  </div>
</template>
<script>
export default {
  data() {
    return {
      counter: 0
    }
  }
}
</script>
```

We're displaying a span tag with a counter data object, which starts at zero. We then have two buttons with `v-on:click` attributes and inline code telling them to increase, or decrease, the counter by one. Then in the script tag, we're initializing that counter through a returned object in the data method.

Now let's take a look at what the same component looks like in Vue 3:

```vue
<template>
  <span>{{ counter }}</span>
  <button @click="counter += 1">+1</button>
  <button @click="counter -= 1">-1</button>
</template>
<script>
import { ref } from 'vue';
export default {
  setup() {
    const counter = ref(0);
    
    return {
      counter
    };
  }
}
</script>
```

The first thing you might notice is that I've **removed that wrapper div** from the template. Previously in Vue, you'd get an error if you tried to render a component with more than one top-level element under the template tag. In Vue 3, this is no longer the case!

Moving down to the script section, it's a little longer than the previous component. That's kind of to be expected though, since our functionality is the bare minimum and there's *slightly* more setup with the composition API. Let's go over the changes line-by-line.

```vue
import { ref } from 'vue';
```

The `ref` method is required in order to give any data point [reactivity](https://v3.vuejs.org/api/refs-api.html) in the composition API. By default, variables returned from the `setup` method are *not* reactive.

```vue
export default {
  setup() { ... }
}
```

Next, we have the new `setup` method. This is the entrypoint for all composition API components, and anything in the returned object from it will be exposed to the rest of our component. This includes things like computed properties, data objects, methods, and component lifecycle hooks.

```vue
setup() {
  const counter = ref(0);
  
  return {
    counter
  };
}
```

We're first creating a counter using the previously-mentioned `ref` method, and passing it the initial value, zero. Then, all we have to do is return that counter, wrapped in an object.

From there, our component works just like it previously did, displaying the current value and allowing the user to adjust it based on the button presses given! Let's move on and take a look at something with a little more moving parts.

## A shopping cart

Moving up in complexity, we'll create a component that uses two common attributes in Vue, computed properties and defined methods. I think a great example for that would be a basic shopping cart component, which shows items that a user has selected on something like an e-commerce website.

Here's an example of that in Vue 2 using the options API:

```vue
<template>
    <div class="cart">
        <div class="row" v-for="(item, index) in items">
            <span>{{ item.name }}</span>
            <span>{{ item.quantity }}</span>
            <span>{{ item.price * item.quantity }}</span>
            <button @click="removeItem(index)">Remove</button>
        </div>
        <div class="row">
            <h3>Total: <span>{{ cartTotal }}</span></h3>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            items: [
                {
                    name: "Cool Gadget",
                    quantity: 3,
                    price: 19.99
                },
                {
                    name: "Mechanical Keyboard",
                    quantity: 1,
                    price: 129.99
                }
            ]
        }
    },
    methods: {
        removeItem(index) {
            this.items.splice(index, 1);
        }
    },
    computed: {
        cartTotal() {
            return this.items.reduce((total, item) => {
                return total += (item.price * item.quantity);
            }, 0);
        }
    }
}
</script>
```

Items in the cart are listed with `v-for`, and a button is present after each one to remove it from the main array on click. The total cost of the cart is calculated through a computed property that uses `reduce` and the value is displayed at the bottom of the items. Pretty straightforward, I think!

Let's see what a similar component with these attributes looks like in Vue 3 using the composition API:

```vue
<template>
    <div class="cart">
        <div class="row" v-for="(item, index) in items">
            <span>{{ item.name }}</span>
            <span>{{ item.quantity }}</span>
            <span>{{ item.price * item.quantity }}</span>
            <button @click="removeItem(index)">Remove</button>
        </div>
        <div class="row">
            <h3>Total: <span>{{ cartTotal }}</span></h3>
        </div>
    </div>
</template>
<script>
import { ref, computed } from 'vue';
export default {
    setup() {
        const items = ref([
            {
                name: "Cool Gadget",
                quantity: 3,
                price: 19.99
            },
            {
                name: "Mechanical Keyboard",
                quantity: 1,
                price: 129.99
            }
        ]);
        
        const removeItem = (index) => {
            items.value.splice(index, 1);
        };
        
        const cartTotal = computed(() => {
            return items.value.reduce((total, item) => {
                return total += (item.price * item.quantity);
            }, 0);
        });
        
        return {
            items,
            removeItem,
            cartTotal
        };
    }
}
</script>
```

The biggest difference is that the computed property and method aren't in their own properties in the root Vue object, instead they're just plain methods defined and returned in the main `setup()` method.

For methods, we just create them as functions:

```vue
const removeItem = (index) => {
    items.value.splice(index, 1);
};
```

And as long as we include them in the returned object, they're exposed to (and can be used by) the rest of the component. Computed properties are almost the exact same, with the exception of being wrapped in a `computed` method that's imported from the main Vue package:

```vue
const cartTotal = computed(() => {
    return items.value.reduce((total, item) => {
        return total += (item.price * item.quantity);
    }, 0);
});
```

This way, **we can de-couple parts of our components** and separate them even further into portions of functionality that can be re-used and imported into multiple other components. We'll see how to do this in our next example.

For instance, if we wanted to, we could easily split out the `cartTotal` computed property or the `removeItem` method into their *own files*. Then instead of defining and using them in the main component above, we'd import them and just call the designated method.

On to the last component!

## A like button

Our third and final example is even more complex than the last two, let's see what a component would look like that has to pull in data from an API endpoint and react to user input.

This is what that might look like with the options API in a Vue 2 application:

```vue
<template>
  <button @click="sendLike" :disabled="isDisabled">{{ likesAmount }}</button>
</template>
<script>
export default {
  data() {
    return {
      likes: 0,
      isDisabled: false
    }
  },
  mounted() {
      fetch('/api/post/1')
          .then((response) => response.json())
          .then((data) => {
              this.likes = data.post.likes;
          });
  },
  methods: {
    sendLike() {
      this.isDisabled = true;
      this.likes++;

      fetch('/api/post/1/likes', {
        method: 'POST'
      })
        .then((response) => {
          this.isDisabled = false;
        }
        .catch((error) => {
          this.likes--;
          this.isDisabled = false;
        });
    }
  },
  computed: {
      likesAmount() {
          return this.likes + ' people have liked this';
      }
  }
}
</script>
```

A little more complicated than our previous examples, but let's break it down.

We're starting off in the template with a button, that has a `v-on:click` bind to a `sendLike` method, and a bound disabled attribute to the data attribute `isDisabled`. Inside of that button we're showing the amount of likes with a `likes` data attribute.

Moving through to the script, we're initializing the data object returned with 0 `likes`, and `isDisabled` set to false. We're using the `mounted()` lifecycle method to call an API endpoint and set the amount of likes to a specific post's likes.

Then we define a `sendLike` method, which disables the button and increases the likes by 1. (We're increasing the likes *before* actually sending the request so that our user interaction is recorded immediately.)

Finally, we send the request to our make-believe API, and await the response. Either way, we remove the disabled attribute from the button, but if the server returns an error for some reason, we remove the initial like that was recorded and reset `likes` to the previous value.

Now, let's see what a similar component would look like in Vue 3 using the composition API:

```vue
<template>
  <button @click="sendLike" :disabled="isDisabled">{{ likesAmount }}</button>
</template>
<script>
import { ref, computed, onMounted } from 'vue';
export default {
  setup() {
    const likes = ref(0);
    const isDisabled = ref(false);
    
    onMounted(() => {
        fetch('/api/post/1')
            .then((response) => response.json())
            .then((data) => {
                likes = data.post.likes;
            });
    });
    
    const sendLike = async () => {
        isDisabled.value = true;
        likes.value++;
        
        fetch('/api/post/1/likes', {
            method: 'POST'
        })
            .then((response) => {
                isDisabled.value = false;
            })
            .catch((error) => {
                likes.value--;
                isDisabled.value = false;
            });
    }
    
    const likesAmount = computed(() => {
        return likes.value + ' people have liked this';
    });
    
    return {
      likes,
      isDisabled,
      likesAmount,
      sendLike
    };
  }
}
</script>
```

Alright, there it is!

Now, a main difference between this and our counter component is the addition of a **mounted** lifecycle hook. Instead of being another separate method like in Vue 2's options API, this is again just written as a function in `setup`, wrapped in an included `onMounted()` method.

This is where the composition API can start to shine with composables. This like button component is getting a little long, and it includes some functionality that could be split out into a separate file and imported instead.

For example, we might want to include the retrieval and updating of likes in different components, so we can create a new JavaScript file which handles just that:

```js
// useLikes.js
import { ref, computed, onMounted } from 'vue';

export default function useLikes(postId) {
    const likes = ref(0);
    const likesAmount = computed(() => {
        return likes + ' people have liked this'
    });
    
    onMounted(() => {
        fetch(`/api/posts/${postId}`)
            .then((response) => response.json())
            .then((data) => {
                likes.value = data.post.likes;
            });
    });
    
    return {
        likes,
        likesAmount
    }
}
```

This renderless component, `useLikes`, initiates the placeholder likes amount, 0. It then sends a fetch request to the API endpoint of the post whose ID is passed in. After that completes, our likes are then updated to match whatever is attributed to that current post.

So, how's this used back in our main component? Like this:

```vue
<template>
  <button @click="sendLike" :disabled="isDisabled">{{ likesAmount }}</button>
</template>
<script>
import { useLikes } from '@/useLikes';
import { ref, computed, onMounted } from 'vue';
export default {
  setup() {
    const {
        likes,
        likesAmount
    } = useLikes(1);
    
    const isDisabled = ref(false);

    const sendLike = async () => {
        isDisabled.value = true;
        likes.value++;
        
        fetch('/api/post/1/likes', {
            method: 'POST'
        })
            .then((response) => {
                isDisabled.value = false;
            })
            .catch((error) => {
                likes.value--;
                isDisabled.value = false;
            });
    }
    
    return {
      likes,
      isDisabled,
      likesAmount,
      sendLike
    };
  }
}
</script>
```

First we use an import statement to get our exported `useLikes` function, and then use a destructured object that consists of the **likes** and **likesAmount** ref object and method respectively. They're brought into our main component through that same `useLikes` function.

All that was left to do was pass in the `postId` attribute, which we've set as a hard-coded value to 1.

## Wrapping up

Well, there you have it! You've seen **three different components** that were created in Vue 2, and then their counterparts replicated in Vue 3.

Whether you're a developer experienced in the framework, or one who's still learning the ropes, I hope these helped you on your journey through this newest version of Vue. Despite its different, sometimes intimidating appearance, the composition API can help you organize and refactor your frontend code in a more stable and maintainable way.

If you have any questions, comments, or want to chat more about web development in general, don’t hesitate to reach out on [Twitter](https://twitter.com/aschmelyun)!