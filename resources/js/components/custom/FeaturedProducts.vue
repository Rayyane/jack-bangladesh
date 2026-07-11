<script setup lang="ts">
// Mock data for the products grid items
const products = [
  { 
    id: 1, 
    name: 'Jack E4S', 
    subText: 'Standard overlock with L/M/H feeding selector' 
  },
  { 
    id: 2, 
    name: 'Jack C5T', 
    subText: 'Digital top feed high-speed overlock machine' 
  },
  { 
    id: 3, 
    name: 'Jack A4C', 
    subText: 'Digitalized flex-stitch lockstitch with auto footlifter' 
  },
  { 
    id: 4, 
    name: 'Jack C7 URUS', 
    subText: 'AI-driven ultra-heavy duty smart fabric cutter' 
  },
  { 
    id: 5, 
    name: 'Jack K7 UT', 
    subText: 'Intelligent cylinder-bed interlock template machine' 
  },
  { 
    id: 6, 
    name: 'Jack W4', 
    subText: 'Flatbed high-speed interlock utility system' 
  },
  { 
    id: 7, 
    name: 'Jack E4S', 
    subText: 'Standard overlock with L/M/H feeding selector' 
  },
  { 
    id: 8, 
    name: 'Jack C5T', 
    subText: 'Digital top feed high-speed overlock machine' 
  },
];
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 font-sans border-t border-border/40">
    
    <div class="mb-16 my-6">
      <h2 class="text-4xl sm:text-3xl font-extrabold tracking-normal text-foreground">
        Featured Products
      </h2>
      <p class="text-lg text-muted-foreground mt-1 font-roboto">
        Engineered for Precision, Built to Last
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-16 justify-center">
      
      <div 
        v-for="product in products" 
        :key="product.id"
        class="relative bg-card border border-border rounded-xl pb-4 px-3.5 text-center shadow-sm hover:shadow-md hover:border-jack-blue/30 transition-all duration-300 group pt-20 my-5 flex flex-col justify-between"
      >
        <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-5/6 max-w-[140px] aspect-[600/550] flex items-center justify-center pointer-events-none">
          <img 
            src="/E4S.png" 
            alt="Product Image" 
            class="w-full h-full object-contain mx-auto transition-transform duration-300 group-hover:-translate-y-1"
          />
        </div>

        <div class="relative z-10 flex-1 flex flex-col justify-start">
          <h3 class="text-xl font-bold text-foreground group-hover:text-jack-blue transition-colors duration-200">
            {{ product.name }}
          </h3>
          <p class="text-lg text-muted-foreground mt-1.5 font-sans leading-relaxed min-h-[2.5rem] line-clamp-2">
            {{ product.subText }}
          </p>
        </div>

        <div class="relative z-10 mt-5 space-y-2 font-roboto">
          <div class="grid grid-cols-2 gap-2">
            <a 
              href="#" 
              class="block text-sm text-foreground bg-muted/10 border border-border/80 rounded-md py-1.5 hover:bg-muted hover:text-jack-blue hover:border-jack-blue/20 transition-all duration-150"
            >
              Where To Buy
            </a>
            <a 
              href="#" 
              class="block text-sm text-foreground bg-muted/10 border border-border/80 rounded-md py-1.5 hover:bg-muted hover:text-jack-blue hover:border-jack-blue/20 transition-all duration-150"
            >
              Leaflet
            </a>
          </div>

          <a 
            href="#" 
            class="block w-full text-sm font-bold text-white bg-jack-blue rounded-md py-2 shadow-sm hover:bg-jack-blue/90 hover:shadow transition-all duration-150"
          >
            See Details
          </a>
        </div>

      </div>

    </div>
  </section>
</template>