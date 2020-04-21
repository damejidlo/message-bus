# Dáme jídlo message bus

## What is this

Message bus is a library (or a collection of libraries) used by Dáme jídlo for **separating different layers and contexts** within it's monolithic backend application.

In Dáme jídlo the main purpose was to
* create an application layer with commands/handlers (e.g. "place order") to separate domain logic from the rest of the application
* get rid of tight coupling of different contexts by using asynchronous event subscribers for secondary tasks (e.g. send notification to customer on order placed)

The core is a general message bus. It can be used in different ways:
* as a **command bus**, handling commands synchronously
* as a **event dispatching system**, with the ability to plug-in asynchronous ways to handle events in different subscribers

The message bus functionality can be enhanced with **middleware**, e.g.:
* logging
* transaction management
* sync/async handling

## Documentation

Pending :) See `tests/Integration` for basic use case examples.
