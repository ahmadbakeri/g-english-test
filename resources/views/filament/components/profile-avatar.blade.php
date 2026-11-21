<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 1rem;">
    <div style="position: relative; width: 120px; height: 120px; border-radius: 9999px; overflow: hidden; border: 4px solid #f3f4f6; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        <img src="{{ auth()->user()->getFilamentAvatarUrl() }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
</div>