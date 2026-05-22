<template>
    <div class="p-10">
        <h1 class="mt-3 mb-3 text-center text-2xl">ミーティングスケジュール</h1>
        <div class="calendar">
            <div class="header empty"></div>

            <div
                v-for="day in days"
                :key="day.date"
                class="header"
            >
                {{ day.label }}
            </div>

            <template
                v-for="hour in hours"
                :key="hour"
            >
                <div class="time">
                    {{ hour }}
                </div>

                <div
                    v-for="day in days"
                    :key="day.date + hour"
                    class="cell"
                >
                    <div
                        v-for="meeting in getMeetings(day.date, hour)"
                        :key="meeting.summary"
                        class="meeting"
                        :style="getMeetingStyle(meeting)"
                    >
                        {{ meeting.summary }}
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
    apiUrl: {
        type: String,
        required: true,
    }
})
const meetings = ref([])
const workingHours = ref({
    start: '10:00',
    end: '19:00',
})
const days = ref([])

onMounted(async () => {
    const response = await axios.get(props.apiUrl)
    meetings.value = response.data.meetings
    workingHours.value = response.data.working_hours
    days.value = [
        ...new Set(
            meetings.value.map(meeting => meeting.date)
        )
    ].map(date => {
        const d = new Date(date)
        const month = d.getMonth() + 1
        const day = d.getDate()

        const weekDays = ['日', '月', '火', '水', '木', '金', '土']

        return {
            date,
            label: `${month}/${day}（${weekDays[d.getDay()]}）`
        }
    })
})

const hours = computed(() => {
    const result = []

    const startHour = Number(
        workingHours.value.start.split(':')[0]
    )

    const endHour = Number(
        workingHours.value.end.split(':')[0]
    )

    for (let hour = startHour; hour <= endHour; hour++) {
        result.push(`${String(hour).padStart(2, '0')}:00`)
    }
    return result
})

const getMeetings = (date, hour) => {
    return meetings.value.filter(meeting => {
        if (meeting.date !== date) {
            return false
        }
        const meetingStartHour = meeting.start.slice(0, 2)
        const cellHour = hour.slice(0, 2)

        return meetingStartHour === cellHour
    })
}

const getMeetingStyle = (meeting) => {

    const startMinutes =
        Number(meeting.start.split(':')[1])

    const startHour =
        Number(meeting.start.split(':')[0])

    const endHour =
        Number(meeting.end.split(':')[0])

    const endMinutes =
        Number(meeting.end.split(':')[1])

    const duration =
        ((endHour * 60 + endMinutes)
        - (startHour * 60 + startMinutes))

    return {
        top: `${(startMinutes / 60) * 80}px`,
        height: `${(duration / 60) * 80}px`,
    }
}

</script>
<style scoped>
.calendar {
    display: grid;
    grid-template-columns: 100px repeat(3, 1fr);
    border: 1px solid #ddd;
    max-width: 900px;
    margin: 0 auto;
}

.header {
    padding: 20px;
    text-align: center;
    border: 1px solid #ddd;
    background: #fff;
}

.empty {
    background: #fff;
}

.time {
    height: 80px;
    padding-top: 10px;
    text-align: center;
    border: 1px solid #ddd;
}

.cell {
    height: 80px;
    border: 1px solid #ddd;
    position: relative;
}

.meeting {
    position: absolute;
    left: 0;
    right: 0;
    background: #4db6ac;
    color: white;
    padding: 12px;
    box-sizing: border-box;
    overflow: hidden;
    z-index: 1;
}
</style>