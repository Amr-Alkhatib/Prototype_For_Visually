# Visually: prototype

**An interactive cache simulator for learning the RISC-V memory hierarchy.** This is my prototype from November 2025. It became **[Visually](https://github.com/Prashantstrugglestocode/Visually)**, which won **2nd place at the TOMFoolery Hackathon at TUM**.

**Final version (live):** https://visually-ppfe.vercel.app · **Final repo:** [Prashantstrugglestocode/Visually](https://github.com/Prashantstrugglestocode/Visually). Team project; I built the React front end and embedded the cache simulation.

<!-- Add a screenshot: put it at docs/screenshot.png and remove these comment markers.
![Prototype dashboard](docs/screenshot.png)
-->

---

## Why

Students learn hit rate and AMAT as formulas without ever seeing a set fill up or a block get evicted. The idea was to make every access visible: which set it maps to, whether it hits or misses, what gets evicted and what that costs in time and energy.

## What the prototype does

- **Configurable L1 cache.** Size, block size, associativity (direct-mapped, 2/4/8-way or fully associative) and replacement policy (LRU, FIFO, Random).
- **Two-level hierarchy.** L1 → L2 (4-way) → RAM, with write tracking (dirty bits).
- **Three kinds of input:** a small RISC-V subset (`LW`, `SW`, `ADD`, `SUB`, `ADDI`), a custom address sequence or a sequential pattern. You can step through it one access at a time, auto-play it, or run it all at once.
- **Per-access breakdown.** Set index, tag, hit or miss and miss type (compulsory / capacity / conflict), plus running hit rate and **AMAT**.
- **Energy model.** Static, dynamic and miss-penalty energy per access, with dynamic energy scaled by V² for voltage experiments.
- **Visualisations.** A system diagram, separate CPU / L1 / L2 / RAM pages, a set-by-way cache grid, an access timeline, signal waveforms and a power-mix chart (Chart.js).
- **Theory page and help assistant.** A built-in explanation of cache concepts, and a rule-based help chat. Real LLM answers were planned but aren't implemented in this prototype.

## Architecture

```
src/
  lib/simulator.js        CacheSimulator: address decoding, L1/L2 lookup, replacement,
                          miss classification, AMAT and the energy model (pure JS)
  hooks/useSimulator.js   React state around the simulator: step, play, run all, reset
  context/                Shared simulator and tooltip state
  components/             Pages (CPU, L1/L2, RAM, Theory, History), dashboard,
                          visualisations, auth overlay, help chat
backend/                  Laravel 11 API scaffold: Sanctum token auth and CRUD for
                          saved simulations (routes, controllers, models, migrations)
```

The simulator is plain JavaScript with no React inside, so it can be tested and reused on its own.

## Run the front end

```bash
npm install
npm run dev
```

The front end runs on its own. The `backend/` folder is only needed for accounts and saved history. It contains the API code (routes, controllers, models, migrations), meant to be dropped into a fresh Laravel 11 project. See `backend/README.md`.

## Known limitations

I'm listing these on purpose. A prototype is where you find out what's hard.

- **Miss classification is simplified.** A miss into a full set counts as *conflict* in a set-associative cache and as *capacity* in a fully associative one. A correct split needs a fully associative LRU shadow cache of the same total size: a miss that also misses in the shadow cache is a capacity miss, and one that hits there is a conflict miss.
- **FIFO currently behaves like LRU.** The same timestamp is refreshed on every hit, so FIFO also evicts the least recently *used* block instead of the oldest *inserted* one. The fix is to store the insertion time separately and refresh the hit timestamp only for LRU.
- **The help chat is rule-based.** The Gemini integration was only stubbed here.

## Tech

React 18 · Vite · React Router · Chart.js · Tailwind CSS · Laravel 11 (Sanctum) · JavaScript
