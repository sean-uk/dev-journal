# README

The purpose of this tool is to make it easier for developers
to document important aspects of a project of theirs with human readable notes.

To be useful, the notes needs to be taggable by subject (ie; _'security'_ or _'issue-M7RVV8XY'_)
and easily filtered by subject _or date_.

It's not meant to replace a proper knowledge base,
but rather to be a low-friction way for developers to compile notes as they go along, and so encourage regular, incremental documentation (which might be later compiled into a user guide or handover document, for instance).

## Example

For instance, imagine you're building a new ecommerce system, where items can be put in a cart and 
it's represented something like this:

```php
    class Item
    {
        /**
         * @var Cart $cart
         */
        private $cart;
        
        public __construct (Cart $cart)
        {
            $this->cart = $cart;
        {
    }
```

... later on you decide you don't wanna burden the Item class with a dependency on what cart it's in,
and instead to represent that relationship in a separate class like this:

```php
    class ItemInCart
    {
        /**
         * @var Item $item
         */
        private $item;
        
        /**
         * @var Cart $cart
         */
        private $cart;
        
        public __construct (Item $item, Cart $cart)
        {
            $this->item = $item;
            $this->cart = $cart;
        }
    }
```

One day, years later, someone's performance tuning your code and wants to revert back to the old way 
because it'll be faster or whatever.

_You or they need to be able to include detailed notes on why you made your design decision!_

Sure, the `ItemInCart` class is a good place for that, _but only if you know to look there in the first place_,
which won't always be the case.

Also, if you need detailed commentry to explain what you were doing, that's gonna really bloat your code.

Furthermore, it's gonna be hard to keep the chronological record straight
in changes to your ecommerce logic, especially if some of these comments are in class docblocks,
some in methods, etc.