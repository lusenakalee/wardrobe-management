<script setup lang="ts">


interface ThreadForm {
  name: string;
  description: string;
  category: string;
  color: string;
  size: string;
  season: string;
  brand: string;
  price: number;
  image_url: string;
  ownership_status: 'owned' | 'rented' | 'borrowed';
  rental_due_date: string;
  borrowed_from: string;
  rented_from: string;
  tags: string[];
  condition: 'new' | 'good' | 'worn out' | 'needs repair';
  laundry_status: 'clean' | 'dirty' | 'dry cleaning' | 'lost';
  wear_count: number;
  last_worn_date: string;
}

interface ThreadResponse {
  data: ThreadForm;
}


import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3';


import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import { valueUpdater } from '@/lib/utils'

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '../../Components/ui/table'
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next'
import { h, ref } from 'vue'
import DropdownAction from './DataTableDemoColumn.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Checkbox } from '@/Components/ui/checkbox'
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu'


const props = defineProps({
  data: Array,
  filter: Array,
  errors: Object,
    auth: Object,
})
const data = props.data;
const columns = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': value => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  },
 
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('name')),
  },
  {
    accessorKey: 'brand',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Brand', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('brand')),
  },
  {
    accessorKey: 'price',
    header: () => h('div', { class: 'text-right' }, 'Price'),
    cell: ({ row }) => {
      const amount = Number.parseFloat(row.getValue('price'))

      // Format the amount as a dollar amount
      const formatted = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount)

      return h('div', { class: 'text-right font-medium' }, formatted)
    },
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('status')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const payment = row.original

      return h(DropdownAction, {
        payment,
        onExpand: row.toggleExpanded,
      })
    },
  },
]

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})

const table = useVueTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
})


const showDialog = ref<boolean>(false);
const showDialogCreate = () => {
  showDialog.value = true;
};

const imageUrl = ref<string[]>([]);
const size = ref<string[]>(['XL', 'L', 'SM']);

const form =  useForm({
  name: '',
  description: '',
  category: '',
  color: '',
  size: '',
  season: '',
  brand: '',
  price: 0,
  image_url: '',
  ownership_status: 'owned',
  rental_due_date: '',
  borrowed_from: '',
  rented_from: '',
  tags: [],
  condition: 'good',
  laundry_status: 'clean',
  wear_count: 0,
  last_worn_date: '',
});

const errors = ref<Record<string, string>>({});

const submit = () => {
  form.size = size.value.toString(); // Convert size array to string
  form.image_url = imageUrl.value; // Assign image URL

  form.post('/threads', {
    preserveState: true,
    onError: (error) => {
      // Show errors
      errors.value = error;
    },
    onSuccess: () => {
      // Show success message
      toast('Item has been created', {
        description: 'Your clothing item has been added successfully.',
        action: {
          label: 'Undo',
          onClick: () => console.log('Undo'),
        },
      });

      // Refresh data
      showDialog.value = false;
      setTimeout(() => {
        window.location.reload();
      }, 3000);
    },
  });
};

const onEdit = async (id) => {
  console.log(id);
  try {
    const res = await fetch(`/threads/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!res.ok) {
      console.error('Error fetching thread data');
    }
    const data: ThreadResponse = await res.json();
    console.log(data);

    // Set form values from fetched data
    form.name = data.data.name;
    form.description = data.data.description;
    form.category = data.data.category;
    form.color = data.data.color;
    form.size = data.data.size;
    form.season = data.data.season;
    form.brand = data.data.brand;
    form.price = data.data.price;
    form.image_url = data.data.image_url;
    form.ownership_status = data.data.ownership_status;
    form.rental_due_date = data.data.rental_due_date;
    form.borrowed_from = data.data.borrowed_from;
    form.rented_from = data.data.rented_from;
    form.tags = data.data.tags;
    form.condition = data.data.condition;
    form.laundry_status = data.data.laundry_status;
    form.wear_count = data.data.wear_count;
    form.last_worn_date = data.data.last_worn_date;

    // Open dialog
    showDialog.value = true;
  } catch (error) {
    console.error(error);
  }
};


</script>

<template>
  <AuthenticatedLayout>
    <template #title>
            Threads Gallery
        </template>



    <div>
  <div class="w-full">
    <div class="flex gap-2 items-center justify-between py-4">
      <div class="flex gap-2">
      <Input
        class="max-w-sm"
        placeholder="Filter names..."
        :model-value="table.getColumn('name')?.getFilterValue() "
        @update:model-value=" table.getColumn('name')?.setFilterValue($event)"
      />
      </div>
      <div class="flex gap-2">
        <Button variant="outline" @click="showDialogCreate">
              <Plus class="h-4"></Plus>
              Create New
            </Button>



        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="ml-auto">
              Columns <ChevronDown class="ml-2 h-4 w-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuCheckboxItem
              v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
              :key="column.id"
              class="capitalize"
              :model-value="column.getIsVisible()"
              @update:model-value="(value) => {
                column.toggleVisibility(!!value)
              }"
            >
              {{ column.id }}
            </DropdownMenuCheckboxItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <template v-for="row in table.getRowModel().rows" :key="row.id">
              <TableRow :data-state="row.getIsSelected() && 'selected'">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
              <TableRow v-if="row.getIsExpanded()">
                <TableCell :colspan="row.getAllCells().length">
                  {{ JSON.stringify(row.original) }}
                </TableCell>
              </TableRow>
            </template>
          </template>

          <TableRow v-else>
            <TableCell
              :colspan="columns.length"
              class="h-24 text-center"
            >
              No results.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <div class="flex items-center justify-end space-x-2 py-4">
      <div class="flex-1 text-sm text-muted-foreground">
        {{ table.getFilteredSelectedRowModel().rows.length }} of
        {{ table.getFilteredRowModel().rows.length }} row(s) selected.
      </div>
      <div class="space-x-2">
        <Button
          variant="outline"
          size="sm"
          :disabled="!table.getCanPreviousPage()"
          @click="table.previousPage()"
        >
          Previous
        </Button>
        <Button
          variant="outline"
          size="sm"
          :disabled="!table.getCanNextPage()"
          @click="table.nextPage()"
        >
          Next
        </Button>
      </div>
    </div>
  </div>

  <Dialog v-model:open="showDialog">
  <DialogContent class="max-w-[1225px]">
    <DialogHeader>
      <DialogTitle>Create Product</DialogTitle>
      <DialogDescription>
        Add a new clothing item to your wardrobe.
      </DialogDescription>
    </DialogHeader>
    <form>
      <div class="grid grid-cols-2 gap-5">
        <!-- Col 1 -->
        <div class="flex flex-col gap-2">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="form.name" />
            <span v-if="errors?.name" class="text-red-600 text-sm">{{ errors.name }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="category">Category</Label>
            <Select v-model="form.category">
              <SelectTrigger>
                <SelectValue placeholder="Select a category" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="Shirt">Shirt</SelectItem>
                  <SelectItem value="Pants">Pants</SelectItem>
                  <SelectItem value="Shoes">Shoes</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <span v-if="errors?.category" class="text-red-600 text-sm">{{ errors.category }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="color">Color</Label>
            <Input id="color" v-model="form.color" />
            <span v-if="errors?.color" class="text-red-600 text-sm">{{ errors.color }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="size">Size</Label>
            <Input id="size" v-model="form.size" />
            <span v-if="errors?.size" class="text-red-600 text-sm">{{ errors.size }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="season">Season</Label>
            <Input id="season" v-model="form.season" />
            <span v-if="errors?.season" class="text-red-600 text-sm">{{ errors.season }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="brand">Brand</Label>
            <Input id="brand" v-model="form.brand" />
            <span v-if="errors?.brand" class="text-red-600 text-sm">{{ errors.brand }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Textarea v-model="form.description" />
            <span v-if="errors?.description" class="text-red-600 text-sm">{{ errors.description }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="image_url">Image URL</Label>
            <Input id="image_url" v-model="form.image_url" />
            <span v-if="errors?.image_url" class="text-red-600 text-sm">{{ errors.image_url }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="price">Price</Label>
            <Input type="number" v-model="form.price" />
            <span v-if="errors?.price" class="text-red-600 text-sm">{{ errors.price }}</span>
          </div>
        </div>
        <!-- Col 2 -->
        <div class="flex flex-col gap-2">
          <div class="grid gap-2">
            <Label for="ownership_status">Ownership Status</Label>
            <Select v-model="form.ownership_status">
              <SelectTrigger>
                <SelectValue placeholder="Select ownership status" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="owned">Owned</SelectItem>
                  <SelectItem value="rented">Rented</SelectItem>
                  <SelectItem value="borrowed">Borrowed</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <span v-if="errors?.ownership_status" class="text-red-600 text-sm">{{ errors.ownership_status }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="rental_due_date">Rental Due Date</Label>
            <Input type="date" v-model="form.rental_due_date" />
            <span v-if="errors?.rental_due_date" class="text-red-600 text-sm">{{ errors.rental_due_date }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="borrowed_from">Borrowed From</Label>
            <Input id="borrowed_from" v-model="form.borrowed_from" />
            <span v-if="errors?.borrowed_from" class="text-red-600 text-sm">{{ errors.borrowed_from }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="rented_from">Rented From</Label>
            <Input id="rented_from" v-model="form.rented_from" />
            <span v-if="errors?.rented_from" class="text-red-600 text-sm">{{ errors.rented_from }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="tags">Tags</Label>
            <Input id="tags" v-model="form.tags" />
            <span v-if="errors?.tags" class="text-red-600 text-sm">{{ errors.tags }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="condition">Condition</Label>
            <Select v-model="form.condition">
              <SelectTrigger>
                <SelectValue placeholder="Select condition" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="new">New</SelectItem>
                  <SelectItem value="good">Good</SelectItem>
                  <SelectItem value="worn out">Worn Out</SelectItem>
                  <SelectItem value="needs repair">Needs Repair</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <span v-if="errors?.condition" class="text-red-600 text-sm">{{ errors.condition }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="laundry_status">Laundry Status</Label>
            <Select v-model="form.laundry_status">
              <SelectTrigger>
                <SelectValue placeholder="Select laundry status" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="clean">Clean</SelectItem>
                  <SelectItem value="dirty">Dirty</SelectItem>
                  <SelectItem value="dry cleaning">Dry Cleaning</SelectItem>
                  <SelectItem value="lost">Lost</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <span v-if="errors?.laundry_status" class="text-red-600 text-sm">{{ errors.laundry_status }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="wear_count">Wear Count</Label>
            <Input type="number" v-model="form.wear_count" />
            <span v-if="errors?.wear_count" class="text-red-600 text-sm">{{ errors.wear_count }}</span>
          </div>
          <div class="grid gap-2">
            <Label for="last_worn_date">Last Worn Date</Label>
            <Input type="date" v-model="form.last_worn_date" />
            <span v-if="errors?.last_worn_date" class="text-red-600 text-sm">{{ errors.last_worn_date }}</span>
          </div>
        </div>
      </div>
    </form>
    <DialogFooter>
      <Button @click="submit">Create</Button>
    </DialogFooter>
  </DialogContent>
</Dialog>


  



    </div>
  </AuthenticatedLayout>
</template>
