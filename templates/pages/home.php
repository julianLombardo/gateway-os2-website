  <section class="hero">
    <div class="hero-content">
      <p class="overline">Bare-Metal x86 Operating System</p>
      <h1>Built from nothing.<br>Into <em>everything</em>.</h1>
      <p class="subtitle">
        GatewayOS2 is a complete operating system written entirely from scratch &mdash;
        every pixel rendered, every packet routed, every keystroke handled &mdash;
        with zero external dependencies.
      </p>
      <div class="hero-buttons">
        <a href="https://github.com/julianLombardo/GatewayOS2" class="btn btn-primary" target="_blank">
          View Source
        </a>
        <a href="/features" class="btn btn-secondary">
          Explore
        </a>
      </div>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="stats-bar">
        <div class="stat">
          <span class="number">50+</span>
          <span class="label">Applications</span>
        </div>
        <div class="stat">
          <span class="number">18K</span>
          <span class="label">Lines of Code</span>
        </div>
        <div class="stat">
          <span class="number">~100KB</span>
          <span class="label">Kernel</span>
        </div>
        <div class="stat">
          <span class="number">0</span>
          <span class="label">Dependencies</span>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="section-header">
        <span class="overline">Capabilities</span>
        <h2>Six pillars of a<br>hand-built system</h2>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="card-number">01</div>
          <h3>Compositing Window Manager</h3>
          <p>Draggable windows with focus tracking, minimize and close controls, and a NeXTSTEP-inspired aesthetic at 1024&times;768 in 32-bit color.</p>
        </div>
        <div class="feature-card">
          <div class="card-number">02</div>
          <h3>Full Networking Stack</h3>
          <p>E1000 NIC driver with Ethernet, ARP, IPv4, UDP, TCP, DHCP, and DNS. Real internet connectivity from bare metal.</p>
        </div>
        <div class="feature-card">
          <div class="card-number">03</div>
          <h3>Java IDE</h3>
          <p>Built-in editor, recursive-descent interpreter with variables, control flow, arrays, and eight sample programs ready to run.</p>
        </div>
        <div class="feature-card">
          <div class="card-number">04</div>
          <h3>PE32 Executable Loader</h3>
          <p>Load and execute Windows PE32 binaries with over sixty shimmed Win32 API functions for compatibility.</p>
        </div>
        <div class="feature-card">
          <div class="card-number">05</div>
          <h3>Security Suite</h3>
          <p>Six cipher algorithms, password analysis, system auditing, port scanning, and hash computation &mdash; all native.</p>
        </div>
        <div class="feature-card">
          <div class="card-number">06</div>
          <h3>Persistent Storage</h3>
          <p>ATA PIO driver for reading and writing to disk. Login credentials, settings, and user data survive reboots.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="teal">
    <div class="pullquote">
      <blockquote>&ldquo;Every line of code &mdash; from bootloader to window manager to TCP/IP stack &mdash; is hand-written.&rdquo;</blockquote>
      <cite>18,000 lines &middot; C/C++ &middot; Assembly</cite>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="section-header">
        <span class="overline">Getting Started</span>
        <h2>Three commands to boot</h2>
      </div>
      <div class="terminal">
        <div class="terminal-header">
          <span class="terminal-dot red"></span>
          <span class="terminal-dot yellow"></span>
          <span class="terminal-dot green"></span>
        </div>
        <div class="terminal-body">
          <span class="comment"># Clone</span><br>
          <span class="prompt">$</span> <span class="cmd">git clone https://github.com/julianLombardo/GatewayOS2.git</span><br><br>
          <span class="comment"># Build (~100KB kernel)</span><br>
          <span class="prompt">$</span> <span class="cmd">make</span><br><br>
          <span class="comment"># Run</span><br>
          <span class="prompt">$</span> <span class="cmd">make run</span><br>
        </div>
      </div>
    </div>
  </section>

  <section class="dark">
    <div class="container">
      <div class="section-header">
        <span class="overline" style="color: var(--burnt-orange);">Under the Hood</span>
        <h2>Technical specifications</h2>
      </div>
      <div class="specs-grid">
        <div class="spec-item">
          <div class="value">x86</div>
          <div class="desc">32-bit Protected Mode</div>
        </div>
        <div class="spec-item">
          <div class="value">C/C++</div>
          <div class="desc">92% of Codebase</div>
        </div>
        <div class="spec-item">
          <div class="value">VESA</div>
          <div class="desc">1024&times;768 @ 32bpp</div>
        </div>
        <div class="spec-item">
          <div class="value">Multiboot</div>
          <div class="desc">GRUB Compatible</div>
        </div>
        <div class="spec-item">
          <div class="value">ATA PIO</div>
          <div class="desc">Persistent Storage</div>
        </div>
        <div class="spec-item">
          <div class="value">PS/2</div>
          <div class="desc">Keyboard &amp; Mouse</div>
        </div>
        <div class="spec-item">
          <div class="value">TCP/IP</div>
          <div class="desc">Full Network Stack</div>
        </div>
        <div class="spec-item">
          <div class="value">No libc</div>
          <div class="desc">Fully Freestanding</div>
        </div>
      </div>
    </div>
  </section>
