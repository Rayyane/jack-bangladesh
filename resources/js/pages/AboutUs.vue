<!-- AboutView.vue -->
<script setup lang="ts">
import Navbar from '@/components/custom/Navbar.vue';
import Footer from '@/components/custom/FooterSection.vue';
// Company milestones / statistics
const stats = [
  { label: 'Global Markets', value: '100+' },
  { label: 'Patents Registered', value: '1,500+' },
  { label: 'Production Units / Year', value: '2M+' },
  { label: 'Years of Innovation', value: '25+' },
];

// Core values or pillars
const pillars = [
  {
    title: 'Intelligent Automation',
    desc: 'Leading the industrial shift toward AI-driven stitch adaptation, direct-drive energy efficiency, and digitalized manufacturing systems.',
    img: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80',
  },
  {
    title: 'Precision Engineering',
    desc: 'Crafted with aerospace-grade durability to withstand non-stop high-speed manufacturing across heavy-duty, denim, and delicate apparel.',
    img: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=800&q=80',
  }
];
</script>

<template>
  <Navbar />
  <div class="min-h-screen bg-background font-sans">
    
    <!-- ========================================== -->
    <!-- HERO HEADER                                -->
    <!-- ========================================== -->
    <section class="bg-jack-blue text-white py-16 lg:py-24 relative overflow-hidden">
      <!-- Background pattern overlay -->
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff0a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff0a_1px,transparent_1px)] bg-[size:24px_24px]"></div>
      
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-orange-300 font-roboto">Who We Are</span>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">Driving the Future of Apparel Technology</h1>
        <p class="max-w-2xl mx-auto text-sm sm:text-base text-white/80 leading-relaxed font-sans">
          Jack Sewing Machine Co. is a global pioneer in intelligent sewing equipment, dedicated to delivering precision, energy efficiency, and high-performance automation to manufacturers worldwide.
        </p>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- STATS MATRIX                               -->
    <!-- ========================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-card border border-border rounded-xl p-6 shadow-xl text-center">
        <div v-for="(stat, idx) in stats" :key="idx" class="p-2 border-r last:border-r-0 border-border/60">
          <div class="text-2xl sm:text-4xl font-extrabold text-jack-blue font-roboto">{{ stat.value }}</div>
          <div class="text-xs text-muted-foreground mt-1 font-medium">{{ stat.label }}</div>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- BRAND STORY & PILLARS                      -->
    <!-- ========================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 space-y-24">
      
      <div 
        v-for="(pillar, idx) in pillars" 
        :key="idx"
        class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16 items-center"
      >
        <div :class="['space-y-4', idx % 2 === 1 ? 'md:order-2' : '']">
          <span class="text-xs font-bold uppercase tracking-wider text-jack-blue font-roboto">Core Pillar</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-foreground tracking-tight">{{ pillar.title }}</h2>
          <p class="text-sm text-muted-foreground leading-relaxed">{{ pillar.desc }}</p>
        </div>

        <div :class="['overflow-hidden rounded-xl border border-border shadow-md aspect-[16/10]', idx % 2 === 1 ? 'md:order-1' : '']">
          <img :src="pillar.img" :alt="pillar.title" class="w-full h-full object-cover" />
        </div>
      </div>

    </section>

    <!-- ========================================== -->
    <!-- CALL TO ACTION BANNER                      -->
    <!-- ========================================== -->
    <section class="bg-muted/30 border-t border-border py-16">
      <div class="max-w-4xl mx-auto px-4 text-center space-y-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-foreground">Ready to Upgrade Your Production Line?</h2>
        <p class="text-sm text-muted-foreground max-w-xl mx-auto font-roboto">
          Explore our range of intelligent lockstitch, overlock, and automatic template machinery built for modern industrial demands.
        </p>
        <div>
          <a href="#" class="inline-block bg-jack-blue text-white font-roboto font-bold text-xs px-8 py-3.5 rounded-lg shadow hover:bg-jack-blue/90 transition-all">
            Explore Full Product Catalog
          </a>
        </div>
      </div>
    </section>

  </div>
  <Footer />
</template>