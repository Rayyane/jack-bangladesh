<script setup lang="ts">
import { ref } from 'vue';
import NavDropdownItem from './NavDropdownItem.vue';

const isMobileSearchOpen = ref(false);
const isMobileMenuOpen = ref(false); // Mobile menu visibility toggle

// Mocking an infinite recursive structure using placeholder data
const mockNavCategories = [
  {
    id: 1,
    name: 'Lockstitch',
    children: [
      {
        id: 11,
        name: 'Overlock',
        children: [
          { id: 111, name: 'Jack E4S (Feeding Selector)', isProduct: true },
          { id: 112, name: 'Jack C5T (Digital Top Feed)', isProduct: true },
          { id: 113, name: 'Jack C7 URUS (Artificial Intelligence)', isProduct: true },
          { id: 114, name: 'Jack C60 (Touch Screen)', isProduct: true },
          { id: 115, name: 'Jack 797 (Small Arm)', isProduct: true },
        ]
      },
      {
        id: 12,
        name: 'Interlock',
        children: [
          { id: 121, name: 'Jack W4 (Flatbed)', isProduct: true },
          { id: 122, name: 'Jack W5E (Flatbed Thread Trimmer)', isProduct: true },
          { id: 123, name: 'Jack K7 UT (Artificial Intelligence)', isProduct: true },
          { id: 124, name: 'Jack K10+- UT (Touch Screen)', isProduct: true },
        ]
      },
      { id: 301, name: 'Jack F6 (Basic)', isProduct: true },
      { id: 302, name: 'Jack A2C (Thread Trimmer)', isProduct: true },
      { id: 305, name: 'Jack A4C (Digital Footlifter & Bartack)', isProduct: true },
    ]
  },
  {
    id: 2,
    name: 'Heavy Duty',
    children: [
      { id: 21, name: 'Jack H2 (Basic Top Feed)', isProduct: true },
      { id: 22, name: 'Jack H5K (Edge Cutter Top Feed)', isProduct: true },
      { id: 23, name: 'Jack H7 (Digitalized Top Feed)', isProduct: true },
      { id: 24, name: 'Jack Z7 (Triple Transport Digital Lockstitch)', isProduct: true },
      { id: 25, name: 'Jack 2030 (Advanced Top Feed)', isProduct: true },
      { id: 26, name: 'Jack 2060 (Triple Transport)', isProduct: true },
    ]
  },
  {
    id: 3, name: 'Post Bed', children: [
      { id: 31, name: 'Jack S5 (Synchronized Rollers)', isProduct: true },
      { id: 32, name: 'Jack S7 (Independant Rollers Touch)', isProduct: true },
    ]
  },
  {
    id: 4,
    name: 'Zig Zag',
    children: [
      { id: 41, name: 'Jack 20U (Short Arm)', isProduct: true },
      { id: 42, name: 'Jack 2280 (Mechanical)', isProduct: true },
      { id: 43, name: 'Jack 2284 (Three Stitch)', isProduct: true },
      { id: 44, name: 'Jack 2290 (Digital)', isProduct: true },
      { id: 45, name: 'Jack 1530 (Heavy Duty)', isProduct: true },
    ]
  },
  {
    id: 5,
    name: 'Special',
    children: [
      { id: 51, name: 'Jack H2 (Basic Top Feed)', isProduct: true },
      { id: 52, name: 'Jack H5K (Edge Cutter Top Feed)', isProduct: true },
      { id: 53, name: 'Jack H7 (Digitalized Top Feed)', isProduct: true },
      { id: 54, name: 'Jack Z7 (Triple Transport Digital Lockstitch)', isProduct: true },
      { id: 55, name: 'Jack 2030 (Advanced Top Feed)', isProduct: true },
      { id: 56, name: 'Jack 2060 (Triple Transport)', isProduct: true },
    ]
  },
  {
    id: 6,
    name: 'Template',
    children: [
      { id: 61, name: 'Jack W4 (Flatbed)', isProduct: true },
      { id: 62, name: 'Jack W5E (Flatbed Thread Trimmer)', isProduct: true },
      { id: 63, name: 'Jack K7 UT (Artificial Intelligence)', isProduct: true },
      { id: 64, name: 'Jack K10+- UT (Touch Screen)', isProduct: true },
    ]
  },
  {
    id: 7,
    name: 'Ironing',
    children: [
      { id: 71, name: 'Jack E4S (Feeding Selector)', isProduct: true },
      { id: 72, name: 'Jack C5T (Digital Top Feed)', isProduct: true },
      { id: 73, name: 'Jack C7 URUS (Artificial Intelligence)', isProduct: true },
      { id: 74, name: 'Jack C60 (Touch Screen)', isProduct: true },
      { id: 75, name: 'Jack 797 (Small Arm)', isProduct: true },
    ]
  },
  {
    id: 8,
    name: 'Automatic',
    children: [
      { id: 81, name: 'Jack 20U (Short Arm)', isProduct: true },
      { id: 82, name: 'Jack 2280 (Mechanical)', isProduct: true },
      { id: 83, name: 'Jack 2284 (Three Stitch)', isProduct: true },
      { id: 84, name: 'Jack 2290 (Digital)', isProduct: true },
      { id: 85, name: 'Jack 1530 (Heavy Duty)', isProduct: true },
    ]
  },
  {
    id: 9,
    name: 'Motors',
    children: [
      { id: 91, name: 'Jack 20U (Short Arm)', isProduct: true },
      { id: 92, name: 'Jack 2280 (Mechanical)', isProduct: true },
      { id: 93, name: 'Jack 2284 (Three Stitch)', isProduct: true },
      { id: 94, name: 'Jack 2290 (Digital)', isProduct: true },
      { id: 95, name: 'Jack 1530 (Heavy Duty)', isProduct: true },
    ]
  },
  {
    id: 10,
    name: 'Accessories',
    children: [
      { id: 101, name: 'Jack F6 (Basic)', isProduct: true },
      { id: 102, name: 'Jack A2C (Thread Trimmer)', isProduct: true },
      { id: 105, name: 'Jack A4C (Digital Footlifter & Bartack)', isProduct: true },
    ]
  },
];
</script>

<template>
  <div class="w-full sticky top-0 z-50 bg-background shadow-sm">

    <header class="bg-jack-blue text-white shadow-md relative z-50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

          <div class="flex items-center gap-2 flex-shrink-0">
            <button @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="inline-flex md:hidden p-2 rounded-md hover:bg-white/10 transition-colors cursor-pointer"
              aria-label="Toggle Navigation Menu">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                  d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <a href="#" class="block">
              <img src="/jacklogo.png" alt="Jack Logo" class="h-9 w-auto object-contain brightness-0 invert" />
            </a>
          </div>

          <div class="hidden md:flex flex-1 max-w-md mx-auto">
            <div class="relative w-full">
              <input type="text" placeholder="Search products, brands, or categories..."
                class="w-full bg-white/10 text-white placeholder-white/70 font-sans border border-white/20 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:bg-white focus:text-foreground focus:placeholder-muted-foreground transition-all duration-200" />
              <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
            </div>
          </div>

          <div class="flex items-center space-x-1 sm:space-x-4 font-roboto">
            <button @click="isMobileSearchOpen = !isMobileSearchOpen"
              class="md:hidden p-2 hover:bg-white/10 rounded-full transition-colors cursor-pointer"
              aria-label="Toggle Search">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path v-if="!isMobileSearchOpen" stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <a href="#"
              class="hidden md:flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-sm font-medium hover:bg-white/10 transition-colors duration-150">
              <svg class="w-4 h-4 text-orange-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1014 0c0-1.307-.349-2.518-1-3.523a11.398 11.398 0 00-3.35-3.882zm-3.86 10.13a.75.75 0 001.05-.143 2.5 2.5 0 014.076.544.75.75 0 001.332-.691 4 4 0 00-6.53-1.096.75.75 0 00.073 1.385z"
                  clip-rule="evenodd" />
              </svg>
              <span class="hidden sm:inline">Hot Deals</span>
            </a>

            <a href="#"
              class="hidden md:flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-sm font-medium hover:bg-white/10 transition-colors duration-150">
              <svg class="w-4 h-4 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
              </svg>
              <span>Contact Us</span>
            </a>

            <a href="#"
              class="flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-sm font-medium hover:bg-white/10 transition-colors duration-150">
              <svg class="w-4 h-4 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h7a3 3 0 013 3v1" />
              </svg>
              <span>Login</span>
            </a>
          </div>

        </div>
      </div>

      <div v-if="isMobileSearchOpen"
        class="md:hidden border-t border-white/10 bg-jack-blue px-4 py-3 animate-in fade-in slide-in-from-top-1 duration-200">
        <div class="relative w-full">
          <input type="text" placeholder="Search products..."
            class="w-full bg-white text-foreground font-sans rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none" />
          <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-4 h-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
        </div>
      </div>
    </header>

    <nav class="hidden md:block relative bg-background border-b border-border z-40" aria-label="Main navigation">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <ul class="flex flex-wrap items-center justify-center gap-x-1 gap-y-1 py-2">

          <li v-for="category in mockNavCategories" :key="category.id"
            class="relative group pt-2 px-3 transition-all duration-100 hover:z-50">
            <button
              class="relative text-foreground hover:text-jack-blue font-medium flex items-center gap-1.5 cursor-pointer pb-1 transition-colors duration-200">
              {{ category.name }}

              <svg v-if="category.children && category.children.length > 0"
                class="w-3 h-3 transition-transform duration-300 transform group-hover:rotate-180 text-muted-foreground group-hover:text-jack-blue"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>

              <span
                class="absolute bottom-0 left-0 w-0 h-[2px] bg-jack-blue transition-all duration-300 ease-out group-hover:w-full"></span>
            </button>

            <div v-if="category.children && category.children.length > 0"
              class="absolute left-1/2 -translate-x-1/2 top-full hidden group-hover:block min-w-56 bg-card border border-border rounded-md shadow-xl py-2 animate-in fade-in slide-in-from-top-2 duration-200">
              <NavDropdownItem v-for="child in category.children" :key="child.id" :item="child" />
            </div>
          </li>

        </ul>
      </div>
    </nav>

    <div v-if="isMobileMenuOpen"
      class="md:hidden absolute top-full left-0 w-full bg-card border-b border-border shadow-2xl z-50 overflow-y-auto max-h-[calc(100vh-4rem)] animate-in fade-in slide-in-from-top-4 duration-200 font-sans">
      <div class="px-4 py-3 space-y-1">
        <div v-for="category in mockNavCategories" :key="'mob-' + category.id"
          class="border-b border-border/50 last:border-0 py-1">

          <div class="flex items-center justify-between py-2 text-foreground font-medium">
            <a href="#" class="hover:text-jack-blue text-sm">{{ category.name }}</a>
          </div>

          <div v-if="category.children && category.children.length > 0" class="pl-4 pb-2 space-y-1.5">
            <div v-for="child in category.children" :key="'mob-child-' + child.id" class="text-xs">
              <span class="text-muted-foreground/70 font-semibold uppercase tracking-wider block text-[10px] mt-1">{{
                child.name }}</span>

              <div v-if="child.children && child.children.length > 0"
                class="mt-1 pl-2 space-y-1 border-l border-border">
                <a v-for="subChild in child.children" :key="'mob-leaf-' + subChild.id" href="#"
                  class="block py-1 text-muted-foreground hover:text-jack-blue font-roboto">
                  {{ subChild.name }}
                </a>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>