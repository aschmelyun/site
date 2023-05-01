---
view: layout.post
title: AI is a fad and programming is dead
description: An attempt at a nuanced take on how things like ChatGPT, AutoGPT, LLMs, and AI in general are going to affect development jobs from here on out.
categories: discussions
published: May 1 2023
excerpt: On one hand, you have people saying that programming as a future career is dead, and it’ll be a couple of years before humans writing code is obsolete. Then there are those who think LLMs like ChatGPT and LLaMA are party tricks, nothing more than a fun experiment that provides minimal value to those building software.
---

These are the two prevailing point of views that I’ve seen spread the most on social media.

On one hand, you have people saying that programming as a future career is essentially dead, and it’ll be a couple of years before humans writing code is made obsolete. Then there are those who think that LLMs like ChatGPT and LLaMA are party tricks, nothing more than a fun little experiment that provides minimal value to those building software.

I think the reality is more nuanced than that, and both points above can be harmful or anxiety-inducing to people who just started in the software engineering field or are interested in it as a career path. I’d like to take a little time and go over what I think AI will change in this landscape over the coming years.

If you’d like a quick breakdown or tl;dr of what’s going to be in this article, the main points are:

- AI will continue to improve, probably in an S-curve fashion, but we don’t know where on the curve we currently sit.
- LLMs are unlikely to take jobs away from software engineers, but they will enable teams and individual contributors to be more efficient, and get more done with less time and resources.
- Tools like ChatGPT usually require you to give detailed instructions in a logical format to yield good code results, this is essentially the role of a programmer just in a new, esoteric language.
- If AI improves to the point that humans are removed completely from the software process then we have made it pretty much to AGI, at which point we have *much* bigger problems to worry about based on our current societal structure.

Still interested? Let’s continue!

## AI improvement and the S-Curve

AI has improved drastically over the course of the last year or so. We’ve gone from some basic sketches and drawings to full-fledged generated movies based on images, text, and speech a la Midjourney and others. OpenAI’s improvements to the GPT model has shown incredible promise in generating text and code. There are AutoGPT experiments, where talented developers have been running processes that spawn LLM entities and have them loop over and perform actions to finish complex multi-step assignments.

All of these might make it seem like we are advancing at an exponential pace in this field, but that might not be the case.

Technological advancements have historically been shown to happen on something called [successive S-curves](https://www.researchgate.net/publication/340525808/figure/fig2/AS:881029480996871@1587065205861/Successive-S-curves-in-technological-development-The-dotted-line-shows-the-overall.png). A technology will slowly advance and then rapidly take off, before slowly plateauing out for a while. A new iteration happens that starts the same cycle over again, but this time at an overall higher level. Zooming out, the graph appears linear but is instead made up of multiple iterations of these curves.

It’s not a bold assumption that AI might follow the same path (at least for now), so the question becomes “where are we on the current curve?”. Maybe toward the bottom, right before accelerating exponentially up, or towards the top with little room to advance at the moment.

If I had to bet on a particular point in the line, I’d say that we’re right before the plateau.

I think we have a small amount of advancement to go with LLMs and the technology that currently surrounds them and AI in general, but I think that we’re missing *something* that will take time to fully provide.

As more and more AI generated content comes out (video, speech, code, images), more and more people can recognize it for its uncanny-valley-ness. Like most projects, I believe that the missing 10% will take 90% of the time and effort to perfect.

How long until we hit that next curve though, and exponential improvement begins again? That’s difficult to say, and no one person should be able to answer that.

## LLMs are the next step in IDE advancement

Getting back on track about programming though, we have seen a drastic improvement and adaptation of models like OpenAI’s GPT in the coding world. Tools like ChatGPT, GitHub Copilot, and now Copilot X have improved the efficiency of developers around the globe.

But, they’re not *replacing* programmers, they’re *tools* in a utility belt enabling programmers to work better.

The first time I went from a text editor like Sublime to a full-fledged JetBrains IDE was *insane*. My productivity skyrocketed as I suddenly had access to auto-completion, automated refactoring, integrated testing, and deeper connections to my project’s dependencies. I was able to produce cleaner code at a dramatically faster rate.

These AI tools are just the next logical step in that same improvement process.

I’ve been using Copilot and ChatGPT in my personal projects since the start of the year, and I have noticed a similar efficiency improvement.

A lot of work done in new projects are tasks I don’t have to put a lot of thought or effort into, but that take up time. Creating automated tests, frontend templates, new classes, or formatting data outputs, I can complete these items 10x faster now that I have the help of these tools. They just *understand* what I’m trying to do, and scaffold out the code necessary in my project to accomplish it.

Going deeper, there have been times where I need to create a complicated function body or work with a library that I’m not super familiar with. I can type in a basic comment for what I’m trying to build in that instant, and the AI spits out a code block that’s usually helpful in satisfying the requirements I’m after.

For an example, I was recently in a PHP application and wanted to work with a popular FFMpeg library with pretty complicated documentation. Instead, I just wrote the following:

```php
public function formatVideo($video)
{
    // use ffmpeg to convert the video to a gif
}
```

Hitting enter after I made that comment produced succinct code that I could use in the rest of the project, with a few minor tweaks.

But the fact of the matter is that **it saved me a ton of time**. If I had *known* the exact details of the library, sure, a minute or two might have been shaved off. But because I was in this realm between knowing the language but not a particular library, I knew exactly what I could ask and could use the code given to me immediately. All without having to take time poring over documentation, Stack Overflow questions, or trial and error.

This is where these tools and AI for programming really shine, it enables you to work *better*.

Two things though.

1. **It’s over-confident.** There have been *multiple* times where the AI has hallucinated arguments or functions that don’t exist in my project or a library I’m using. I might be able to nudge it in the right direction after a few retries, but sometimes it goes completely off the rails and writes code that straight up does not accomplish what I wanted it to. This has been pretty rare, though.
2. **It’s not connected.** ChatGPT and Copilot are great at creating *snippets* of code that perform a specific function from given input. An entire application is still a bit out of reach. First, there’s a barrier for the amount of text, as even GPT-4 is limited by 8K tokens (around ~6k words). Second, even with the use of vector databases and AutoGPT, the models have a hard time sticking to a single development style or accomplishing an overall directive in a program that’s just above moderately complex. Ask them to create a todo app and it’s likely to be fully functional. Ask for something like a CRM to handle leads for a barbershop and it’s more likely to start on the right track, but end up with missing functionality or ineffectual code.

That last part in particular brings us squarely to our next section.

## Programmers are translators for a logic language

If you ask a lot of people what their definition of a programmer is, you’ll likely get a lot of responses that boil down to “someone who writes code”. And while this *isn’t incorrect*, it’s also missing a huge part. A programmer, software engineer, developer, or whatever title you choose, is a *translator* for a language that deals in logic.

Your goal as a programmer is to take a concept, an idea, or a workflow in a language that you can understand, and translate that to a language that a computer can understand. This programming language is designed to prevent ambiguous statements and deal in pure logic.

Let’s take the sentence “When the button is pressed, change the background to red”.

If you’re a person in a meeting with other people from your team, you all might intuitively know exactly what is meant by that.

But if you’re a computer, you have a ton of missing information. What button? What background? What shade of red? What if it’s pressed again?

We can redefine our sentence again to try to remove ambiguity. “When the button with the ID of ‘clicky’ is pressed, change the background of that same button to a color with the hex value #FF0000”

Written in JavaScript it looks like this:

```js
document.getElementById('clicky').addEventListener('click', function() {
	this.style.backgroundColor = "#FF0000"
})
```

If you’re familiar with this programming language, and you were given the code above and asked to explain what it does, you might produce a sentence similar to the second one above.

You’ve *translated* JavaScript into your native language.

This is the heart and essence of programming, and is one of the biggest reasons I believe that the profession will be around for quite a while, even in the face of advancing AI tools.

There’s hundreds and thousands of threads online where people ask “Why isn’t ChatGPT producing the code I want it to?” and inevitably the answer comes down to:

“You need to know how to talk to it.”

Well, if I need to use a *specific* language to talk to this tool and get back accurate data every time, then I’m just *programming with a natural language*. This isn’t a new concept, it’s just that with the breadth and complexity that LLMs offer, the barrier for entry is lower. 

Even if building an application has been reduced down to typing in prompts in a tool like ChatGPT, if you have to use a specific language to get it to create a reliable output that works every time, you’re in essence *still programming*.

Looking at the present, trying to build an application with an LLM with the current limitations of the models means that you’re likely going to have to put some of the pieces together yourself. This still constitutes programming, and you’ll need to know some basics about *what* the language is that it produced code for you in and *where* to put the pieces it gave you.

But let’s say that things advance to the point where that’s trivial or useless, because the AI will do it for you anyway. You say “Alright, compile these assets and publish them on example.com”. At that point, why even *have* a programming language? Let the AI be the layer between your data and your result, skipping the middle step entirely.

Well at that point, we’re basically at [AGI](https://en.wikipedia.org/wiki/Artificial_general_intelligence).

## AGI replaces everything

Let’s say that everything above has been perfected and there needs to be almost zero human intervention needed. You can simply have a prompt attached to a database (also managed by an AI) where you can simply ask for any dashboard, result set, or functionality that you could from a program. What then?

If AI is powerful enough to remove human intervention and oversight from the programming realm, then it has gotten to the point that it can replace almost every creative and knowledge worker profession in existence. This would likely lead to a worldwide economic downturn as more than 60% of the labor force is no longer required.

At the very least, we’d need [UBI](https://en.wikipedia.org/wiki/Universal_basic_income), or taken to an extreme, [FALC](https://en.wikipedia.org/wiki/Fully_Automated_Luxury_Communism). 

This of course implies that every business, everywhere, would hop on this technology immediately. While most companies are always looking to maximize profits and increase efficiency, a lot of the business world turns at a slower pace than the technology realm likes to believe.

I know teams that are just *now* picking up tools like Docker, or working with frameworks like React. The enterprise world moves at a *very* slow pace, and even if a tool was provided that could perform 90% of a team’s job developing new products, maintaining legacy software and complex inter-dependent systems will still need to be done during, or even after, an adaptation of an AI system.

The software engineering profession has some ~100k open positions in the US alone, even if complete automation was made available today, there would be a large amount of time before full adaptation was reached.

## Wrapping up

This piece was mostly written as a way of getting a bunch of different thoughts I’ve had about AI and the programming profession out of my brain. It’s not a secret that I’m a very anxious person, and I won’t lie and say that I’m not nervous about my future as a software engineer.

However, I’m excited for the tools that are being created to increase my productivity and allow me to be a better programmer. I encourage anyone out there interested in this career, or just starting out, to keep learning and utilizing the tools and techniques available to you.

I think that we’re going to see a lot of interesting advancements in both AI and the software development world over the next few years, and it’s an exciting time to be in the field. I also believe that given the current trejectory, this profession **is not** a dead end.

Developers may become a lot more efficient over time, leading to smaller team sizes and available company roles. However, startups and smaller enterprises can utilize the same technology and low barrier of entry to launch new products and services that before, would have taken more time and effort.

If I’m completely wrong about the above and programming is obsolete in a few years, well then it’s been a fun ride and you can catch me in a cabin in the woods gardening and making cabinetry.