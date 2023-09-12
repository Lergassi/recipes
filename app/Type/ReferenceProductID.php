<?php

namespace App\Type;

//todo: Для удобства будет называться ...ID. Нужно найти решение, чтобы назывался ReferenceProductID, а не с Alias.
enum ReferenceProductID: string
{
    case Water = 'water';
    case Rice = 'rice';
    case Pasta = 'pasta';
    case Buckwheat = 'buckwheat';
    case Bulgur = 'bulgur';
    case Chicken = 'chicken';
    case Pork = 'pork';
    case Beef = 'beef';
    case Carrot = 'carrot';
    case Onion = 'onion';
    case Garlic = 'garlic';
    case Flour = 'flour';
    case Egg = 'egg';
    case Oil = 'oil';
    case Olive_oil = 'olive_oil';
    case Sugar = 'sugar';
    case Bread = 'bread';
    case Lasagna = 'lasagna';
    case Celery = 'celery';
    case Milk = 'milk';
    case Tomato_paste = 'tomato_paste';
    case Butter = 'butter';
    case Tvorog = 'tvorog';
    case Semolina = 'semolina';
    case Salt = 'salt';
    case Dill = 'dill';
    case Ajika = 'ajika';
    case Black_pepper = 'black_pepper';
    case Chili_pepper = 'chili_pepper';
    case Garlic_powder = 'garlic_powder';
    case Cumin = 'cumin';
    case Rosemary = 'rosemary';
    case Thyme = 'thyme';
    case Basil = 'basil';
    case Khmeli_suneli = 'khmeli_suneli';
    case Utskho_suneli = 'utskho_suneli';
    case Coriander = 'coriander';
    case Nutmeg = 'nutmeg';
    case Mutton = 'mutton';
    case Wine = 'wine';
    case Tomatoes_ss = 'ss_tomatoes';
    case Parmigiano_reggiano = 'parmigiano_reggiano';
    case Cheese = 'cheese';
}
