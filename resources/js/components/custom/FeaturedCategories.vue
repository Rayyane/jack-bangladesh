<script setup lang="ts">
// Mock data for the 6 category grid items
const categories = [
  { id: 1, name: 'Lockstitch Machines' },
  { id: 2, name: 'Overlock Series' },
  { id: 3, name: 'Heavy Duty' },
  { id: 4, name: 'Automatic Templates' },
  { id: 5, name: 'Genuine Motors' },
  { id: 6, name: 'Spare Parts & Tools' },
  { id: 7, name: 'Ironing' },
  { id: 8, name: 'Automatic' },
  { id: 9, name: 'Motors' },
  { id: 10, name: 'Zig Zag' },
  { id: 11, name: 'Post Bed' },
  { id: 12, name: 'Interlock Series' },
];
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 font-sans">
    
    <div class="mb-16 my-6">
      <h2 class="text-4xl sm:text-3xl font-extrabold tracking-normal text-foreground">
        Featured Categories
      </h2>
      <p class="text-lg text-muted-foreground mt-1 font-roboto">
        Curated Just For You
      </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-16 justify-center">
      
      <div 
        v-for="category in categories" 
        :key="category.id"
        class="relative bg-card border border-border rounded-xl pb-5 px-4 text-center shadow-sm hover:shadow-md hover:border-jack-blue/30 transition-all duration-300 group pt-20 my-5"
      >
        <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-5/6 max-w-[140px] aspect-[600/550] flex items-center justify-center pointer-events-none">
          <img 
            src="/1790.png" 
            alt="Category Placeholder" 
            class="w-full h-full object-contain mx-auto transition-transform duration-300 group-hover:-translate-y-1"
          />
          
          <!-- <div class="absolute inset-0 border border-dashed border-muted-foreground/30 rounded flex items-center justify-center bg-muted/30 backdrop-blur-[1px]">
            <span class="text-[10px] font-mono text-muted-foreground/60">600x550 image</span>
          </div> -->
        </div>

        <div class="relative z-10">
          <h3 class="text-xs sm:text-sm font-bold text-foreground group-hover:text-jack-blue transition-colors duration-200 line-clamp-2 min-h-[2.5rem] flex items-center justify-center">
            {{ category.name }}
          </h3>
        </div>

      </div>

    </div>
  </section>
</template>