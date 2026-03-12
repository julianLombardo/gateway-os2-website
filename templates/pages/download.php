  <section class="hero" style="padding: 5rem 3rem 3rem;">
    <div class="hero-content">
      <p class="overline">Get It</p>
      <h1>Download<br><em>GatewayOS2</em></h1>
      <p class="subtitle">Clone the source, build the kernel, and boot your own operating system.</p>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="download-grid">
        <div class="download-card primary">
          <div class="download-badge">Recommended</div>
          <h3>Source Code</h3>
          <p>Clone the full repository and build from source. You'll need an i686-elf cross-compiler, NASM, and QEMU.</p>
          <div class="terminal" style="margin: 1.5rem 0;">
            <div class="terminal-header">
              <span class="terminal-dot red"></span>
              <span class="terminal-dot yellow"></span>
              <span class="terminal-dot green"></span>
            </div>
            <div class="terminal-body">
              <span class="prompt">$</span> <span class="cmd">git clone https://github.com/julianLombardo/GatewayOS2.git</span><br>
              <span class="prompt">$</span> <span class="cmd">cd GatewayOS2 && make</span><br>
              <span class="prompt">$</span> <span class="cmd">make run</span>
            </div>
          </div>
          <a href="https://github.com/julianLombardo/GatewayOS2" class="btn btn-primary" target="_blank" rel="noopener" style="width: 100%; justify-content: center;">
            View on GitHub
          </a>
        </div>

        <div class="download-card">
          <h3>Latest Release</h3>
          <p>Download the pre-built kernel ELF or bootable ISO from GitHub Releases.</p>
          <div class="release-info">
            <div class="release-row">
              <span class="release-label">Version</span>
              <span class="release-value">v1.1</span>
            </div>
            <div class="release-row">
              <span class="release-label">Kernel Size</span>
              <span class="release-value">~100 KB</span>
            </div>
            <div class="release-row">
              <span class="release-label">Format</span>
              <span class="release-value">ELF / ISO</span>
            </div>
          </div>
          <a href="https://github.com/julianLombardo/GatewayOS2/releases" class="btn btn-secondary" target="_blank" rel="noopener" style="width: 100%; justify-content: center; margin-top: 1rem;">
            GitHub Releases
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Build Requirements -->
  <section>
    <div class="container">
      <div class="section-header">
        <span class="overline">Prerequisites</span>
        <h2>Build requirements</h2>
      </div>
      <div class="specs-grid" style="max-width: 800px;">
        <div class="spec-item">
          <div class="value">MSYS2</div>
          <div class="desc">Windows Environment</div>
        </div>
        <div class="spec-item">
          <div class="value">i686-elf</div>
          <div class="desc">Cross-Compiler</div>
        </div>
        <div class="spec-item">
          <div class="value">NASM</div>
          <div class="desc">Assembler</div>
        </div>
        <div class="spec-item">
          <div class="value">QEMU</div>
          <div class="desc">Emulator</div>
        </div>
      </div>
    </div>
  </section>

  <section class="dark">
    <div class="container">
      <div class="section-header">
        <span class="overline" style="color: var(--burnt-orange);">Step by Step</span>
        <h2>Build instructions</h2>
      </div>
      <div class="accordion">
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">1. Install MSYS2 &amp; cross-compiler</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>Install <a href="https://www.msys2.org/" target="_blank" rel="noopener" style="color: var(--burnt-orange);">MSYS2</a> on Windows. Then install the i686-elf cross-compiler toolchain. The build system expects the binaries at:</p>
            <div class="code-block" style="margin: 1rem 0;">
              <div class="code-label">Expected path</div>
              <pre style="padding: 1rem;"><code>/c/msys64/opt/cross/bin/i686-elf-gcc
/c/msys64/opt/cross/bin/i686-elf-g++
/c/msys64/opt/cross/bin/i686-elf-ld</code></pre>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">2. Install NASM &amp; QEMU</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>Install NASM (the Netwide Assembler) for assembling the bootloader, and QEMU for testing the OS in a virtual machine.</p>
            <div class="code-block" style="margin: 1rem 0;">
              <div class="code-label">MSYS2</div>
              <pre style="padding: 1rem;"><code><span class="prompt">$</span> <span class="cmd">pacman -S nasm qemu</span></code></pre>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">3. Clone &amp; build</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>Clone the repository, run <code style="color: var(--burnt-orange);">make</code>, and the kernel compiles to <code style="color: var(--burnt-orange);">gateway2.elf</code> (~100KB).</p>
            <div class="code-block" style="margin: 1rem 0;">
              <div class="code-label">Terminal</div>
              <pre style="padding: 1rem;"><code><span class="prompt">$</span> <span class="cmd">git clone https://github.com/julianLombardo/GatewayOS2.git</span>
<span class="prompt">$</span> <span class="cmd">cd GatewayOS2</span>
<span class="prompt">$</span> <span class="cmd">make</span></code></pre>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">4. Run in QEMU</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>Use <code style="color: var(--burnt-orange);">run.bat</code> to launch in QEMU with networking and a virtual disk for persistent storage. The script auto-creates <code style="color: var(--burnt-orange);">userdata.img</code> on first run.</p>
            <div class="code-block" style="margin: 1rem 0;">
              <div class="code-label">Terminal</div>
              <pre style="padding: 1rem;"><code><span class="prompt">$</span> <span class="cmd">./run.bat</span>
<span class="cmt"># Or manually:</span>
<span class="prompt">$</span> <span class="cmd">qemu-system-i386 -kernel gateway2.elf \</span>
  <span class="cmd">  -m 128M -vga std \</span>
  <span class="cmd">  -netdev user,id=net0 -device e1000,netdev=net0 \</span>
  <span class="cmd">  -drive file=userdata.img,format=raw</span></code></pre>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">5. Create bootable ISO (optional)</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>Generate a bootable ISO image to run on real hardware via USB or CD.</p>
            <div class="code-block" style="margin: 1rem 0;">
              <div class="code-label">Terminal</div>
              <pre style="padding: 1rem;"><code><span class="prompt">$</span> <span class="cmd">make iso</span>
<span class="cmt"># Flash to USB:</span>
<span class="prompt">$</span> <span class="cmd">dd if=gatewayos2.iso of=/dev/sdX bs=4M</span></code></pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
