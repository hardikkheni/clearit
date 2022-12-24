<template>
  <component :is="wrapper" v-if="countCharge || client.bondType == '1'">
    <span v-if="countCharge">
      <sup><span class="text-danger">*</span></sup> There have been ({{ countCharge }}) HTS code lookups requested
      <br />
    </span>
    <span v-if="client.bondType == '1'">
      <sup><span class="text-danger">*</span></sup> One-time bond user
    </span>
  </component>
</template>

<script>
import { BListGroupItem } from 'bootstrap-vue'

export default {
  components: {
    BListGroupItem,
  },
  props: {
    countCharge: {
      type: Number,
      required: true,
    },
    client: {
      type: Object,
      required: true,
    },
    wrapper: {
      type: String,
      default: 'div',
    },
  },
}
</script>
