---
title: The satisfaction in treating your side projects like bonsai
slug: the-satisfaction-in-treating-your-side-projects-like-bonsai
description: It wasn't enough to go from a serial starter, to a serial finisher. Here's how I iteratively maintain and grow the projects that I launch.
categories: productivity
published_at: Feb 6 2020
excerpt: I’m a serial starter. If half-finished projects were dollars, I’d be a millionaire. A little over a year ago I wrote an article about overcoming my issues finishing things that I started, but in the end that lead to a new, unforeseen problem, maintaining what I launch.
---

**I’m a serial starter.** If half-finished projects were dollars, I’d be a millionaire. A little over a year ago I [wrote an article](https://medium.com/@aschmelyun/how-i-went-from-abandoning-projects-to-actually-getting-stuff-done-41f02a64faa1) about overcoming my issues finishing things that I started, but in the end that lead to a new, unforeseen problem: **maintaining what I launch.**

What I didn’t realize is that I moved that addiction forward. Where I used to live in the thrill of creating something new, I’m now chasing those same feelings in putting the final touches on a project and getting it out to the world. Unfortunately, that’s still not ideal. If I don’t *maintain* what I launch, fix inevitable bugs and create desirable updates that benefit the users, it will slowly spiral down and fade out of existence.

So instead I’ve started adopting a new stance, **treating my side projects like bonsai**, and it’s given me a new drive when it comes to building something.

> The purposes of bonsai are primarily contemplation for the viewer, and the pleasant exercise of effort and ingenuity for the grower. By contrast with other plant cultivation practices, bonsai is not intended for production of food or for medicine. Instead, bonsai practice focuses on long-term cultivation and shaping of one or more small trees growing in a container.

**Why bonsai?**

For those who might be unfamiliar with the process, bonsai is a Japanese art form, a method of growing and shaping trees or other woody plants into miniature forms of their full-grown selves. From the outside this process might seem relatively simple, yet it takes a tremendous amount of both effort and time to yield a traditional result.

The more I started learning about bonsai, the more I started drawing parallels to the side projects I created. I **wanted** to see my apps grow and flourish, but I **knew** that as one-man, bootstrapped operations it was unlikely to become some mighty oak. However, I didn’t want that to stop me from putting regular, calculated development towards it. To shape it, trim it, wait for growth, and determine what the next steps would be.

**That’s what I’m about to outline for you.**

## Start small, grow iteratively

Just like any plant, you start with a seed. Planted in soil and compost, watered regularly, and nurtured with a little bit of luck, germination occurs and you have the beginnings of a tree. This germination is reflected in my project launch, my MVP, my working product. I’ve created an app/website that serves a complete and purposeful function, putting it out there for the world to see and consume.

**Don’t spend time trying to create the perfect, flawless project.** Some might disagree with me on my next point, but as a single developer and small creator, I just don’t believe that exists. Of course, there’s plenty of checks and balances that you can put in place (using TDD, error monitoring tools, regular and thorough testing) that can get you close to that goal, but getting to 100% certainty is a snipe hunt. Having my project in a “good” state with a public release is better in my eyes than trying to hit “perfect” and never getting it launched.

Alright, I’ve gone through some testing and have a working MVP out on a public channel, now what? **I make it known to the world.** Honestly that sounds incredibly cheesy, but my point is made. I let an audience know that my project exists and is ready for feedback from potential users.

This publicity takes the form of a variety of different channels, all depending on the context of what was created. The usual suspects include sites like Reddit, Twitter, Dev.to, Hacker News, BetaList, and Product Hunt. I make sure that I get it in front of the eyes of the people who would need or use it the most, since that’s whose feedback I value the most.

At this initial point, there’s not a whole lot to do but sit back and watch my traffic start spiking, the initial growth phase of my bonsai tree. It’s a seedling, rapidly reaching toward the sky and getting its leaves out as fast as possible. I relax and keep a regular eye on things, making sure that my server isn’t melting down or there’s not a blatant, glaring issue anywhere.

I let this phase of my project happen organically, maturing into a sapling and slowly winding down in growth before the next step: maintenance.

## Continuously maintain

On all of my projects I make sure to have both [Google Analytics](https://analytics.google.com) and [Google Search Console](https://search.google.com/search-console) accounts set up to monitor referral traffic as well as SEO-based organic growth. The former would come from people posting about your project on an external site like Reddit, Twitter, or Facebook, or from your own launch posts on Product Hunt or BetaList.

Organic growth takes a while to start coming through, and can be compounded on by regularly creating blog posts, informative articles, or content pages relevant to your project’s niche. The trade-off in time and effort is that you’ll be gaining a more stable, consistent type of traffic.

As views start increasing, so do the chances for feature requests and bug reports, and that means decisions to make. **Just like with an actual bonsai tree, I need to have my project’s final form in my mind so that I can trim and shape the right virtual branches as I build to that imagined picture.** For instance, let’s say that I just built a brand new CMS. It’s pretty barebones, sure, but it gets the job done and is attracting a good amount of attention.

I receive the following:

- **A request to add in a calendar to schedule posts.** Does this fit with the goal I originally had (or currently have) in mind for my project? Will this feature provide a use to the users I have, or want to have?

- **A bug that’s affecting password resets.** I should make this a priority if I want to keep the growth of my app up. Ignoring it for much longer could start to impact returning users and potentially get me some bad press.

- **A publication that wants to feature my CMS in an article.** How much traffic does this outlet usually get? I might see a boost of new users, and if so, should I start scaling up my infrastructure? It doesn’t need my immediate focus, but I’ll set a reminder to do it before the article publishes.

Three types of feedback. Three virtual branches. Each one potentially yielding a different outcome to the final project.

To the eager developer, it might make sense to try and knock these all out as fast as possible. In my opinion, and with the theme of this entire article, it’s better to have some patience when approaching these and I follow a simple feedback-fed loop:

**Step 1: Determine priority.** Figure out which of these changes is most requested, most urgent, or needs attention right away. Determine what can wait the longest. Put them into a list, Kanban board, or other type of organizational pattern so you know where they stand.

**Step 2: Complete the first one.** Fix that bug or work on the feature that’s currently at the top of my priority stack. If it’s a big item, I break it down into smaller bite-sized tasks and work through each one in regular intervals. Remember, it’s a bonsai tree, not bamboo, speed shouldn’t be a factor.

**Step 3: Publicize the changes.** After publishing the new feature, I let it be known to the community. Whether that’s through a post on Twitter, an update in Indie Hackers, or by sending out an email to my subscribers, I get users in there who are eager to check it out, and whose opinion I trust.

**Step 4: Wait for feedback.** Keep an eye on analytics and Search Console impressions, see if anything’s changed since I pushed out that new feature or bug fix. Increase in users? Decrease in time spent on your app? More bug requests than usual? Compliments from users on social media? **This step requires patience and time.** So sit back and relax for a week or two minimum.

**Step 5: Reprioritize.** Look back on the priority list with my new feedback in mind. Maybe an item that was lower in the list should be moved to the top now, because users are starting to ask about it. Determine what should be done next.

**Step 6: Go to Step 1 and repeat.**

Working through this continuous loop of feedback → prioritize → develop gives me a pattern of organic, user-fed growth, and shapes my bonsai (project) into a fully-formed and mature platform, regardless of its size.

## Move on when it’s time

There’s always going to be a moment where a project comes to a close, where no matter how much water or nutrients or sunshine that I provide to my bonsai, it could just whither away in front of my eyes. It could have been that I prioritized the wrong feature and my users got tired of waiting for one they actually wanted, or a bug didn’t get fixed properly and sprang up multiple times after promising that it was gone.

Predicting what’ll cause a project’s downfall, or seeing it coming, is a skill that I haven’t exactly acquired yet. I’m hopeful that with enough website and app launches that I’ll start to see the warning signs earlier on. However, there’s no shame in looking at what you’ve built, grown, and cherished, and deciding that it’s time to move on to something else.

I use what caused this failure as knowledge of what not to do next time. The more times that I’ve failed to grow, or burned out too fast, or prioritized incorrectly, the less of a chance I have of it happening for each subsequent project that I create. I take the time afterwards to focus on what I could have done better. Not beating myself up about it, but **using it in a positive light and attributing it to my natural growth** as a developer and serial starter.

## What's next

I hope this wall of text helped you a bit when it comes to maintaining and growing your side projects. I’m honestly not sure what I was trying to convey when I started. I just decided to talk about what’s helped me climb over that wall of inaction, as I used to leave side projects to decay.
I’m hoping to continue to grow and learn about what I build as I move forward. Like bonsai, it’s definitely a process that takes time, effort, and self-reflection. If you have any questions about this or would like to discuss web development and the issues surrounding the field in depth, please don’t hesitate to reach out to me on [Twitter](https://twitter.com/aschmelyun).