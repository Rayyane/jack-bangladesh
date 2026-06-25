<script setup lang="ts">
import NavDropdownItem from './NavDropdownItem.vue';

// Mocking an infinite recursive structure using placeholder data
const mockNavCategories = [
  {
    id: 1,
    name: 'Electronics',
    children: [
      {
        id: 11,
        name: 'Computers & Laptops',
        children: [
          { id: 111, name: 'MacBook Pro M4 Pro', isProduct: true },
          { id: 112, name: 'HP Pavilion Ultra', isProduct: true },
          { id: 113, name: 'Asus ROG Strix', isProduct: true },
        ]
      },
      {
        id: 12,
        name: 'Smartphones',
        children: [
          { id: 121, name: 'iPhone 17 Pro Max', isProduct: true },
          { id: 122, name: 'Google Pixel 10', isProduct: true }
        ]
      },
      { id: 13, name: 'Audio Devices', children: [] } // Will show products directly if mock added
    ],
    products: [
        { id: 99, name: 'Ecotank Printer', isProduct: true }
    ]
  },
  {
    id: 2,
    name: 'Apparel & Fashion',
    children: [
      { id: 21, name: 'Premium Leather Boots', isProduct: true },
      { id: 22, name: 'Minimalist Windbreaker', isProduct: true },
    ]
  },
  {
    id: 3,
    name: 'Home & Living',
    children: [] // Simple link with no nested layers
  }
];
</script>

<template>
  <nav class="relative bg-background border-b border-border z-50" aria-label="Main navigation">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <ul class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1 py-3">
        
        <li 
          v-for="category in mockNavCategories" 
          :key="category.id" 
          class="relative group py-2 px-3"
        >
          <button class="relative text-foreground hover:text-jack-blue font-medium flex items-center gap-1.5 cursor-pointer pb-1 transition-colors duration-200">
            {{ category.name }}
            
            <svg 
              v-if="category.children && category.children.length > 0"
              class="w-3 h-3 transition-transform duration-300 transform group-hover:rotate-180 text-muted-foreground group-hover:text-jack-blue" 
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>

            <span class="absolute bottom-0 left-0 w-0 h-[2px] bg-jack-blue transition-all duration-300 ease-out group-hover:w-full"></span>
          </button>

          <div 
            v-if="category.children && category.children.length > 0"
            class="absolute left-1/2 -translate-x-1/2 top-full hidden group-hover:block min-w-56 bg-card border border-border rounded-md shadow-xl py-2 mt-1 animate-in fade-in slide-in-from-top-2 duration-200"
          >
            <NavDropdownItem 
              v-for="child in category.children" 
              :key="child.id" 
              :item="child" 
            />
          </div>
        </li>

      </ul>
    </div>
  </nav>
</template>