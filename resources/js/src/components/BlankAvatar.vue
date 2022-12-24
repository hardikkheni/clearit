<template>
  <span class="b-avatar rounded-sm">
    <span class="b-avatar-img">
      <img :src="src" />
    </span>
  </span>
</template>

<script>
export default {
  props: {
    src: {
      type: String,
      required: true,
    },
  },
}
</script>
