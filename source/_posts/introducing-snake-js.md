---
extends: _layouts.post
title: "Introducing SnakeJs"
author: "Grzegorz Różycki"
date: 2022-06-16
categories:

- front-end
- game-development
- javascript

---

# Introducing SnakeJs, a small side-project

Side project, pet project call it as you wish… I wanted to have some time with front end and game development. I
stumbled on an old (started in 2015) project of mine, called [SnakeJs](https://github.com/grzegorz-rozycki/snake-js).
I decided to give it some more love and time.

## What is it?

As you can already tell by the name it's a snake game clone written in JavaScript. The code was written quite some time
ago (7 years to be precise as of time of this article). The JavaScript ecosystem and my experience was different back
then. It's a very simple implementation that ought to resemble
[the Snake video game](https://en.wikipedia.org/wiki/Snake_(video_game_genre)). It's implemented in Vanilla JS and uses
HTML5 canvas.

### Current state of the project

Initially the project used bootstrap 2 for styles and used it's modal component.
After some time playing the game I decided to remove those dependencies as they added more bloat than value IMO.
The modal wasn't necessary and harmed the UX in my opinion. An easier solution was to just restart the game by pushing
a button. The logic is placed in separate JavaScript files:

- `js/controls.js` - handles user interactions via keyboard.
- `js/graphics.js` - renders the game.
- `js/physics.js` - game logic.
- `js/snake.js` - ties all above modules together; implements game loop.
- `index.html` - app template; initializes game.

Currently, there is no assets pre-processing required. All files are loaded as are into `index.html`
and the logic is initialized in `window.onload`. The CSS is very minimalistic, so much that I decided to inline it into
the `index.html` file.

I pretty much like this setup for its simplicity. Publishing the app doesn't require a build process.
Even when loading the files separately [Lighthouse](https://developer.chrome.com/blog/lighthouse-load-performance/)
in Chrome Devtools doesn't even complain about that.

### How the game works

This section describes the main parts of the game, how are they implemented and how do they fit together.

#### Displaying the game state - `graphics.js`

Graphics uses the canvas HTML element. It grabs its 2d context and uses the draw rect.
You configure some drawing settings like the fill and stroke styles. It's useful to
think of the context as a stack. You can call `save()` on it and a checkpoint is made so the settings can be restored
when you call `restore()` on the context.

In the `Graphics` object you can find some configuration values like:

- `tileSize`
- `mapWidth`
- `mapHeight`
- `backgroundColor`
- `borderColor`
- `fruitColor`
- `snakeColor`

The methods of interest are:

- `drawMap`
- `drawFruit`
- `drawSnake`

It's important to call them in the right order because their effects are drawn on top of each other.
The recommended order would be to draw the map first, then the fruit and the snake last.

#### Interacting with user input - `controls.js`

We have to control the snake somehow. For that we're using the keyboard (not very mobile friendly)
by listening to keyboard events. We're interested in keyup events more as the keydown events are fired simultaneously
when the key is pressed. All we need to do is call `document.addEventListener('keyup', keyUpHandler)`.

There are three properties on the `Controls` object:

- `actions` - you can think of it as an enum of the allowed movement directions.
- `actionQueue` - a fifo queue of actions that the user triggered.
- `bindings` - a map of key codes to actions to trigger.

You can have a look at the `createDefaults` method to see how the codes are mapped to actions.

#### Rules of the game - `physics.js`

Rules for ending the game, checking collisions and moving the snake are placed in the `phyiscs.js` file.
The main entry point to the logic is the `step` method. It should be called from the game loop to update the game state.
In case a fruit was collected or the snake collided with the boarders or own body events are dispatched.
We register handlers for those events in the `snake.js` file.

#### Concept of time; the game loop - `snake.js`

The `snake.js` holds some configuration values, interacts with the DOM and ties the logic together.
It also implements the game loop, a concept that is present in all interactive games.
It's responsible for controlling the game speed by calling the physics step method in given intervals.
We use `requestAnimationFrame` instead of `setTimeout` or `setInterval` as it's more suited for game development.

This piece of the application initializes all DOM nodes and registers event handlers for when fruits are collected
or collisions have occurred, so it can increment the score or end the game.

## What are my plans?

As you already heard the code base is pretty old, so I'd like to improve it.
My wishlist of improvements currently contains:

- Creating some deployment pipeline so the code gets published to some static site host (probably GitHub Pages, or
  Cloudflare Pages).
- Modernize the code base: use modern JS syntax or even TypeScript.
- Setup a bundler / minimizer / compiler (Webpack, Parcel or Rollup).
- Add unit tests.

I also have some more far-fetched ideas (more on the bottom of the TODO list) like:

- improving the game on mobile; adding some on-screen controls and better support for different screen sizes.
- porting the game to [Elm](https://elm-lang.org/). I'm learning Elm currently, and I thought it could be a good
  exercise.

If this sounds interesting I encourage you to follow me along on this endeavour by subscribing to notifications on
the [GitHub repository](https://github.com/grzegorz-rozycki/snake-js) for this project or
[this site](https://github.com/grzegorz-rozycki/grit.pl) in general.

If you have any suggestions I also encourage you to start
a [discussion on GitHub](https://github.com/grzegorz-rozycki/snake-js/discussions).
