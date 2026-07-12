<div style="min-height: 100vh; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0f9ff 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">

    <style>
        .absen-card {
            width: 100%;
            max-width: 26rem;
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px -12px rgba(3, 105, 161, 0.15), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .absen-header {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 60%, #0ea5e9 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .absen-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .absen-logo {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .absen-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin: 0 0 0.25rem 0;
        }
        .absen-header p {
            font-size: 0.85rem;
            color: #bae6fd;
            margin: 0;
        }
        .absen-header .time-badge {
            display: inline-block;
            margin-top: 0.75rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .absen-body {
            padding: 1.5rem;
        }

        .absen-alert {
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert-icon {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem;
            cursor: pointer;
        }
        .form-select:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            background-color: #ffffff;
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #0369a1, #0284c7);
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.025em;
            box-shadow: 0 4px 6px -1px rgba(3, 105, 161, 0.3);
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #075985, #0369a1);
            box-shadow: 0 6px 10px -2px rgba(3, 105, 161, 0.4);
            transform: translateY(-1px);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .absen-footer {
            text-align: center;
            padding: 1rem 1.5rem 1.5rem;
            border-top: 1px solid #f1f5f9;
        }
        .absen-footer p {
            font-size: 0.7rem;
            color: #94a3b8;
            margin: 0;
        }
    </style>

    <div class="absen-card">
        <div class="absen-header">
            <div class="absen-logo">📚</div>
            <h1>G-ENGLISH</h1>
            <p>{{ __('Confirm Your Attendance') }}</p>
            <div class="time-badge" x-data="{ time: '' }" x-init="setInterval(() => { const d = new Date(); const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; time = days[d.getDay()] + ', ' + d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear() + ' • ' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0'); }, 1000)" x-text="time"></div>
        </div>

        <div class="absen-body">

            @if($errorMessage)
                <div class="absen-alert alert-error">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            @if($successMessage)
                <div class="absen-alert alert-success">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $successMessage }}</span>
                </div>
            @endif

            <form wire:submit.prevent="submit">

                <div class="form-group">
                    <label class="form-label" for="roleType">{{ __('I am a') }}</label>
                    <select wire:model.live="roleType" id="roleType" class="form-select">
                        <option value="student">👩‍🎓 {{ __('Student') }}</option>
                        <option value="tutor">👨‍🏫 {{ __('Tutor') }}</option>
                        <option value="employee">💼 {{ __('Employee / Admin') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="personId">{{ __('Full Name') }}</label>
                    <select wire:model="personId" id="personId" class="form-select">
                        <option value="">— {{ __('Select Your Name') }} —</option>
                        @foreach($options as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    ✅ {{ __('Confirm Attendance') }}
                </button>

            </form>
        </div>

        <div class="absen-footer">
            <p>© {{ date('Y') }} G-English • {{ __('Digital Attendance System') }}</p>
        </div>
    </div>

</div>
