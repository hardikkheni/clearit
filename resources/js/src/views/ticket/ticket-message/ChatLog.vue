<template>
  <div class="chats">
    <div v-for="(msgChunk, index) of computedMessages" :key="index" class="chat" :class="{ 'chat-left': !msgChunk.isMaster }">
      <div class="chat-avatar">
        <b-avatar
          v-b-tooltip.hover.v-primary
          size="36"
          class="avatar-border-2 box-shadow-1"
          v-bind="{
            text: `${msgChunk.name || 'System'}`.substr(0, 2),
            title: `${msgChunk.name || 'System'}`,
          }"
        />
      </div>
      <div class="chat-body">
        <div v-for="(msg, i) of msgChunk.messages" :key="i" class="chat-content">
          <span :style="{ fontSize: '11px' }">
            {{ `${msg.name || 'System'}` }} at {{ dayjs(msg.createdOn).format('MM-DD-YYYY h:m A') }}
          </span>
          <p v-html="msg.message" />
          <a v-if="msg.messageFile" :href="msg.file_url" download>
            <b-badge :variant="`${!msgChunk.isMaster ? 'light' : 'flat'}-primary`">
              <feather-icon icon="DownloadIcon" /> {{ msg.messageFile }}
            </b-badge>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { BAvatar, VBTooltip, BBadge } from 'bootstrap-vue'
import { computed } from '@vue/composition-api'

import dayjs from 'dayjs'

export default {
  components: {
    BAvatar,
    BBadge,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  props: {
    messages: {
      type: Array,
      required: true,
    },
  },
  setup(props) {
    const computedMessages = computed(() => {
      const msgChunks = []
      const firstMsg = (props.messages && props.messages[0]) || {}
      let msgChunk = { isMaster: firstMsg.isMaster || false, messages: [], userid: firstMsg?.userid, name: firstMsg?.name }
      props.messages.forEach((msg, i) => {
        if (msgChunk.name === msg.name) {
          msgChunk.messages.push(msg)
        } else {
          msgChunks.push(msgChunk)
          msgChunk = { isMaster: msg.isMaster || false, messages: [msg], userid: msg.userid, name: msg.name }
        }
        if (i === props.messages.length - 1) msgChunks.push(msgChunk)
      })
      return msgChunks
    })
    return {
      computedMessages,
      dayjs,
    }
  },
}
</script>
