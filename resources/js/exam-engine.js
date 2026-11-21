class ExamEngine {
    constructor() {
        this.config = window.CBT_CONFIG;
        this.state = {
            currentQuestion: 1,
            totalQuestions: document.querySelectorAll('.question-card').length,
            answers: {},
            doubts: new Set(),
            violations: 0,
            timeRemaining: this.config.timeRemaining || 0,
            lastSaveTime: Date.now()
        };
        
        this.init();
    }

    init() {
        this.storageKey = `g_english_cbt_attempt_${this.config.attemptId}`;
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                this.state.answers = { ...this.state.answers, ...JSON.parse(saved) };
            }
        } catch (e) {}

        // Initial setup
        if (this.state.totalQuestions > 0) {
            this.showQuestion(1);
        } else {
            const loader = document.getElementById('loading-overlay');
            if (loader) loader.classList.add('hidden');
            
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');
            if (btnPrev) btnPrev.disabled = true;
            if (btnNext) btnNext.disabled = true;
        }

        // Setup Local countdown timer
        setInterval(() => this.tickTimer(), 1000);

        // Anti-cheat: Disable right click, copy, paste, inspect element
        this.setupKeyboardMouseLock();

        // Initial answers scan (if reloaded)
        this.scanExistingAnswers();
    }

    setupKeyboardMouseLock() {
        // Disable Right Click
        document.addEventListener('contextmenu', e => e.preventDefault());
        
        // Disable Copy, Cut, Paste
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
        
        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        document.addEventListener('keydown', e => {
            if (e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) || // Ctrl+Shift+I/J
                (e.ctrlKey && e.keyCode === 85) // Ctrl+U
            ) {
                e.preventDefault();
                return false;
            }
        });
    }

    scanExistingAnswers() {
        document.querySelectorAll('.option-radio:checked').forEach(radio => {
            const qNum = radio.closest('.question-card').id.split('-')[2];
            this.state.answers[qNum] = radio.value;
            this.updateNavGrid(qNum, 'answered');
        });
        this.updateAnsweredCount();
    }

    showQuestion(qNum) {
        document.querySelectorAll('.question-card').forEach(el => el.classList.add('hidden', 'flex-col'));
        document.querySelectorAll('.question-card').forEach(el => el.classList.remove('flex'));
        
        const card = document.getElementById(`question-card-${qNum}`);
        if (card) {
            card.classList.remove('hidden');
            card.classList.add('flex');
            this.state.currentQuestion = qNum;
            this.updateButtonStates();
        }

        // Remove loading overlay if present
        const loader = document.getElementById('loading-overlay');
        if (loader) loader.classList.add('hidden');
    }

    nextQuestion() {
        if (this.state.currentQuestion < this.state.totalQuestions) {
            this.showQuestion(this.state.currentQuestion + 1);
        }
    }

    prevQuestion() {
        if (this.state.currentQuestion > 1) {
            this.showQuestion(this.state.currentQuestion - 1);
        }
    }

    jumpToQuestion(qNum) {
        this.showQuestion(qNum);
        
        // Close mobile sidebar if open
        const sidebar = document.getElementById('mobile-sidebar');
        if (sidebar && !sidebar.classList.contains('translate-x-full')) {
            sidebar.classList.add('translate-x-full');
        }
    }

    updateButtonStates() {
        document.getElementById('btn-prev').disabled = this.state.currentQuestion === 1;
        document.getElementById('btn-next').disabled = this.state.currentQuestion === this.state.totalQuestions;
    }

    selectAnswer(qNum, qId, answer) {
        this.state.answers[qId] = answer;
        this.updateNavGrid(qNum, 'answered');
        this.updateAnsweredCount();
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.state.answers));
        } catch (e) {}
    }

    toggleDoubt(qNum) {
        const btn = document.getElementById(`btn-doubt-${qNum}`);
        const navBtn = document.getElementById(`nav-btn-${qNum}`);
        
        if (this.state.doubts.has(qNum)) {
            // Remove doubt
            this.state.doubts.delete(qNum);
            
            btn.classList.remove('bg-amber-100', 'text-amber-700', 'border-amber-300');
            btn.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
            
            if (this.state.answers[qNum]) {
                if(navBtn) navBtn.setAttribute('data-status', 'answered');
            } else {
                if(navBtn) navBtn.setAttribute('data-status', 'unanswered');
            }
        } else {
            // Add doubt
            this.state.doubts.add(qNum);
            
            btn.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
            btn.classList.add('bg-amber-100', 'text-amber-700', 'border-amber-300');
            
            if(navBtn) navBtn.setAttribute('data-status', 'doubt');
        }
    }

    updateNavGrid(qNum, status) {
        const navBtn = document.getElementById(`nav-btn-${qNum}`);
        if (!navBtn) return;

        // If it's marked as doubt, doubt color takes precedence
        if (this.state.doubts.has(qNum)) {
            navBtn.setAttribute('data-status', 'doubt');
            return;
        }

        if (status === 'answered' || this.state.answers[qNum]) {
            navBtn.setAttribute('data-status', 'answered');
        } else {
            navBtn.setAttribute('data-status', 'unanswered');
        }
    }

    updateAnsweredCount() {
        const count = Object.keys(this.state.answers).length;
        document.getElementById('answered-count').innerText = count;
    }

    // API Calls
    async saveSingleAnswer(qId, answer) {
        try {
            await fetch(`${this.config.baseUrl}/exam/${this.config.attemptId}/auto-save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                body: JSON.stringify({ question_id: qId, answer: answer })
            });
            this.state.lastSaveTime = Date.now();
        } catch (e) {
            console.error("Auto-save failed", e);
        }
    }

    async autoSave() {
        // Just a ping or bulk save if implemented
        // Currently saving on every click, so this can be a heartbeat
    }

    async fetchTimeRemaining() {
        try {
            const res = await fetch(`${this.config.baseUrl}/exam/${this.config.attemptId}/time`);
            const data = await res.json();
            if (data.status === 'success') {
                this.state.timeRemaining = data.time_remaining_seconds;
            }
        } catch (e) {
            console.error("Time sync failed", e);
        }
    }

    tickTimer() {
        if (this.state.timeRemaining > 0) {
            this.state.timeRemaining--;
            this.updateTimerDisplay();
        } else if (this.state.timeRemaining === 0) {
            // Force submit when time is up
            this.state.timeRemaining = -1; // Prevent multiple triggers
            this.forceSubmit('Waktu Ujian Habis!');
        }
    }

    updateTimerDisplay() {
        const display = document.getElementById('timer-display');
        const container = document.getElementById('timer-container');
        const icon = document.getElementById('timer-icon');
        
        let totalSeconds = Math.floor(this.state.timeRemaining);
        let h = Math.floor(totalSeconds / 3600);
        let m = Math.floor((totalSeconds % 3600) / 60);
        let s = totalSeconds % 60;

        display.innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

        // Color coding
        if (totalSeconds <= 60) {
            // Blinking red for last minute
            container.classList.add('bg-rose-100', 'border-rose-300', 'animate-pulse');
            display.classList.add('text-rose-700');
            icon.classList.add('text-rose-500');
        } else if (totalSeconds <= 300) {
            // Red for last 5 mins
            container.classList.remove('bg-white/80', 'animate-pulse');
            container.classList.add('bg-rose-50', 'border-rose-200');
            display.classList.add('text-rose-700');
            icon.classList.add('text-rose-500');
        } else if (totalSeconds <= 600) {
            // Yellow for last 10 mins
            container.classList.add('bg-amber-50', 'border-amber-200');
            display.classList.add('text-amber-700');
            icon.classList.add('text-amber-500');
        }
    }

    // Submit Actions
    requestSubmit() {
        const modal = document.getElementById('submit-modal');
        const warning = document.getElementById('unanswered-warning');
        
        const answered = Object.keys(this.state.answers).length;
        if (answered < this.state.totalQuestions) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
    }

    closeSubmitModal() {
        const modal = document.getElementById('submit-modal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    async submitExam() {
        const btn = document.getElementById('btn-confirm-submit');
        btn.innerHTML = `<i class="ph ph-spinner animate-spin"></i> Memproses...`;
        btn.disabled = true;

        try {
            const res = await fetch(`${this.config.baseUrl}/exam/${this.config.attemptId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                body: JSON.stringify({ answers: this.state.answers })
            });
            const data = await res.json();
            try { localStorage.removeItem(this.storageKey); } catch (e) {}
            
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (e) {
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            btn.innerHTML = `<i class="ph ph-check"></i> Ya, Kumpulkan`;
            btn.disabled = false;
        }
    }

    async forceSubmit(reason = 'Force Submit') {
        alert(`${reason} Ujian sedang dikumpulkan...`);
        try {
            const res = await fetch(`${this.config.baseUrl}/exam/${this.config.attemptId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                }
            });
            const data = await res.json();
            if (data.redirect) window.location.href = data.redirect;
        } catch (e) {
            window.location.href = `${this.config.baseUrl}/exam/${this.config.attemptId}/result`;
        }
    }

    // Anti-Cheat: Visibility (Disabled)
    async handleVisibilityChange() {
        // Disabled per user request
    }
}

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    window.examEngine = new ExamEngine();
});
