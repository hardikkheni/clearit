<template>
  <div class="ticket-message-card">
    <b-card bg-variant="transparent" class="chat-widget mb-0" no-body border-variant="primary">
      <b-card-title>
        <feather-icon size="30" icon="MessageSquareIcon" />
        Ticket Message
      </b-card-title>
      <hr />
      <section class="chat-app-window">
        <vue-perfect-scrollbar ref="refChatLogPS" :settings="perfectScrollbarSettings" class="user-chats scroll-area">
          <chat-log :messages="messages" />
        </vue-perfect-scrollbar>
        <quill-editor v-model="chatInputMessage" :options="editorOptions" />
        <div id="chat-editor-toolbar" class="clearfix border-bottom-0">
          <button class="ql-bold" />
          <button class="ql-italic" />
          <button class="ql-underline" />
          <button class="ql-strike" />
          <button class="ql-list" value="ordered" />
          <button class="ql-list" value="bullet" />
          <button class="ql-link" />
          <button class="ql-align" value="center" />
          <button class="ql-align" value="right" />
          <button class="ql-align" value="justify" />
          <button class="ql-blockquote" />
          <button class="ql-code-block" />
          <button class="ql-clean" />
          <b-form-file ref="fileInput" v-model="file" type="hidden" plain />
          <b-button variant="flat-primary" class="file-label float-right">
            <feather-icon icon="SendIcon" />
          </b-button>
          <b-button variant="flat-primary" class="file-label float-right mr-05" @click.prevent="triggerFileInput">
            <feather-icon icon="PaperclipIcon" :badge="fileName.fullName" />
          </b-button>
        </div>
      </section>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardTitle, BButton, BFormFile, VBTooltip } from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { quillEditor } from 'vue-quill-editor'

import ChatLog from '@/views/ticket/ticket-message/ChatLog.vue'

export default {
  components: { BCard, BCardTitle, VuePerfectScrollbar, ChatLog, quillEditor, BButton, BFormFile },
  directives: {
    'b-tooltip': VBTooltip,
  },
  props: {
    messages: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      perfectScrollbarSettings: {
        // maxScrollbarLength: 150,
        wheelPropagation: false,
      },
      editorOptions: {
        modules: {
          toolbar: '#chat-editor-toolbar',
        },
        placeholder: 'Message',
      },
      chatInputMessage: '',
      file: null,
    }
  },
  computed: {
    fileName() {
      return {
        name: this.file ? `${this.file.name?.substr(0, 15)}${this.file.name?.length > 15 ? '...' : ''}` : '',
        fullName: this.file ? `${this.file.name}` : '',
      }
    },
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.$el.click()
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/quill.scss';
@import '@core/scss/base/pages/app-chat-list.scss';
</style>

<style lang="scss" scoped>
@import '~@core/scss/base/bootstrap-extended/include';

.ticket-message-card ::v-deep {
  #chat-editor-toolbar {
    border: 1px solid $primary !important;
    border-top: unset !important;
    border-radius: 0rem 0rem 0.5rem 0.5rem;
    .file-label {
      width: unset;
    }
  }
  .quill-editor {
    .ql-container.ql-snow {
      border: 1px solid $primary !important;
      border-bottom: unset !important;
      border-radius: 0.5rem 0.5rem 0rem 0rem;
    }
  }
  .form-control-file {
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
    z-index: -1;
  }
  .mr-05 {
    margin-right: 0.5rem;
  }
}
</style>
