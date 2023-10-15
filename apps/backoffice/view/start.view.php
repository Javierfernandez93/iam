<div class="container py-6" id="app">
    <start-viewer @select-step="selectStep" :step="step"></start-viewer>

    <metatrader-viewer v-if="step == 1" @next-step="nextStep" :feedback="true"></metatrader-viewer>
    <connect-viewer v-if="step == 2" @next-step="nextStep" :feedback="true"></connect-viewer>
    <signal-viewer v-if="step == 3" @next-step="nextStep" :feedback="true"></signal-viewer>
</div>