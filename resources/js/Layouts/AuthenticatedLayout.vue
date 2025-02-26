<script setup lang="ts">
import { ref } from 'vue';

import AppSidebar from '@/Components/AppSidebar.vue'
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList } from '@/Components/ui/breadcrumb'
import Separator from '@/Components/ui/separator/Separator.vue'
import SidebarInset from '@/Components/ui/sidebar/SidebarInset.vue'
import SidebarProvider from '@/Components/ui/sidebar/SidebarProvider.vue'

const showingNavigationDropdown = ref(false);
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <header class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
        <div class="flex items-center gap-2 px-4">
          <SidebarTrigger class="-ml-1" />
          <Separator orientation="vertical" class="mr-2 h-4" />
          <Breadcrumb>
            <BreadcrumbList>
              <BreadcrumbItem class="hidden md:block">
                <BreadcrumbLink href="#">
                 Wardrobe Manager
                </BreadcrumbLink>
              </BreadcrumbItem>
              <BreadcrumbSeparator class="hidden md:block" />
              <BreadcrumbItem>
                <BreadcrumbPage> <slot name="title"></slot></BreadcrumbPage>
              </BreadcrumbItem>
            </BreadcrumbList>
          </Breadcrumb>
        </div>
      </header>
      <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
       <slot/>
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
