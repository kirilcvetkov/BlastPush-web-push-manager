<template>
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white mr-2">
        <slot name="icon"></slot>
      </span>
      <slot name="title"></slot>
    </h3>

    <nav aria-label="breadcrumb">
      <ul class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">
          <slot name="title"></slot>
          <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
  export default {
    props: {
      icon: {
        default: 'mdi-view-dashboard'
      },
      title: {
        default: 'Page Title'
      }
    }
  }
</script>
