<script setup lang="ts">
defineOptions({
  name: 'NavDropdownItem'
});

interface NavItem {
  id: number;
  name: string;
  isProduct?: boolean;
  children?: NavItem[];
  products?: NavItem[]; // Added to hold direct products alongside children
}

defineProps<{
  item: NavItem;
}>();
</script>

<template>
  <div v-if="item.isProduct">
    <a 
      href="#" 
      class="block px-4 py-2 text-xs text-muted-foreground hover:text-foreground hover:bg-muted font-roboto transition-colors duration-150"
    >
      {{ item.name }}
    </a>
  </div>

  <div v-else class="relative group/sub">
    <button class="w-full text-left flex items-center justify-between px-4 py-2.5 text-sm text-foreground hover:bg-muted hover:text-jack-blue cursor-pointer transition-colors duration-150">
      {{ item.name }}
      
      <svg 
        v-if="(item.children && item.children.length > 0) || (item.products && item.products.length > 0)"
        class="w-3 h-3 text-muted-foreground group-hover/sub:text-jack-blue transition-transform duration-300 transform group-hover/sub:rotate-90" 
        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7-7" />
      </svg>
    </button>

    <div 
      v-if="(item.children && item.children.length > 0) || (item.products && item.products.length > 0)"
      class="absolute left-[98%] top-0 hidden group-hover/sub:block min-w-56 bg-card border border-border rounded-md shadow-xl py-2 animate-in fade-in slide-in-from-left-2 duration-200"
    >
      <template v-if="item.children && item.children.length > 0">
        <NavDropdownItem 
          v-for="subChild in item.children" 
          :key="'cat-' + subChild.id" 
          :item="subChild" 
        />
      </template>

      <hr 
        v-if="(item.children && item.children.length > 0) && (item.products && item.products.length > 0)" 
        class="border-border my-1.5"
      />

      <template v-if="item.products && item.products.length > 0">
        <div class="px-4 py-1 text-[10px] font-bold uppercase tracking-wider text-muted-foreground/60 font-sans">
          Direct Products
        </div>
        
        <NavDropdownItem 
          v-for="product in item.products" 
          :key="'prod-' + product.id" 
          :item="{ ...product, isProduct: true }" 
        />
      </template>
    </div>
  </div>
</template>