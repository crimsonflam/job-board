# What "bootstrap" means in this project

The word **bootstrap** shows up in a few places in this codebase, and it means **two completely different things**. Here is the distinction.

---

## 1. `bootstrap/` — Laravel's startup folder (the main meaning)

When the documentation mentions "bootstrap", it refers to [bootstrap/app.php](bootstrap/app.php).

Here "to bootstrap" is the general computing term: **the code that starts up and assembles the application** before it can handle any request.

It's like turning a car's ignition — `bootstrap/app.php` wires everything together (routing, middleware, error handling) and hands back a ready-to-use app object.

The request flow:

```
public/index.php  →  bootstrap/app.php  →  routes → controllers → ...
   (front door)        (startup/wiring)
```

This folder has **nothing to do with styling**.

---

## 2. Bootstrap the CSS framework — **NOT used here**

There is a famous front-end library also called **Bootstrap** (buttons, grids, navbars).

This project does **not** use it — it uses **Tailwind CSS** instead, loaded in [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php).

---

## 3. A confusing third one — `resources/js/bootstrap.js`

There is also a file [resources/js/bootstrap.js](resources/js/bootstrap.js).

That is the **startup** meaning again (it boots up JavaScript libraries like axios) — still **not** the CSS framework.

---

## Summary

| Where you see "bootstrap" | What it means |
|---------------------------|---------------|
| `bootstrap/app.php` (and the `bootstrap/` folder) | Startup / wiring code that boots the Laravel app |
| `resources/js/bootstrap.js` | Startup code that boots front-end JS libraries |
| Bootstrap CSS framework | **Not used in this project** (it uses Tailwind CSS) |

**Every "bootstrap" in this codebase means *"startup / initialization code."* None of them refer to the Bootstrap CSS framework.**
