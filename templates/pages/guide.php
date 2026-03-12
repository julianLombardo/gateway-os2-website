  <section class="hero" style="padding: 5rem 3rem 3rem;">
    <div class="hero-content">
      <p class="overline">Technical Guide</p>
      <h1>From power-on<br>to <em>desktop</em></h1>
      <p class="subtitle">Trace the complete journey of GatewayOS2 — every layer, every decision, every byte.</p>
    </div>
  </section>

  <!-- Interactive Boot Sequence -->
  <section class="alt">
    <div class="container">
      <div class="section-header">
        <span class="overline">Phase One</span>
        <h2>The Boot Sequence</h2>
        <p>What happens in the first milliseconds after power-on</p>
      </div>
      <div class="boot-sequence">
        <div class="boot-step active" data-step="0">
          <div class="boot-step-marker">
            <div class="boot-dot"></div>
            <div class="boot-line"></div>
          </div>
          <div class="boot-step-content">
            <h3>BIOS / POST</h3>
            <p>The machine powers on. BIOS runs Power-On Self-Test, initializes hardware, and searches for a bootable device. It finds the GRUB bootloader on disk.</p>
            <div class="code-block">
              <div class="code-label">boot/boot.asm</div>
              <pre><code><span class="kw">section</span> .multiboot
<span class="kw">align</span> <span class="num">4</span>
    <span class="kw">dd</span> <span class="num">0x1BADB002</span>          <span class="cmt">; Multiboot magic number</span>
    <span class="kw">dd</span> <span class="num">0x00000007</span>          <span class="cmt">; Flags: align + meminfo + video</span>
    <span class="kw">dd</span> -(0x1BADB002 + 0x07) <span class="cmt">; Checksum (must sum to zero)</span></code></pre>
            </div>
          </div>
        </div>
        <div class="boot-step" data-step="1">
          <div class="boot-step-marker">
            <div class="boot-dot"></div>
            <div class="boot-line"></div>
          </div>
          <div class="boot-step-content">
            <h3>Protected Mode Entry</h3>
            <p>GRUB loads the kernel ELF binary, sets up the Multiboot info structure, and jumps to the kernel entry point. The CPU transitions from 16-bit real mode into 32-bit protected mode with a flat memory model.</p>
            <div class="code-block">
              <div class="code-label">boot/boot.asm</div>
              <pre><code><span class="kw">global</span> _start
<span class="kw">extern</span> kernel_main

_start:
    <span class="kw">mov</span>  esp, stack_top     <span class="cmt">; Set up the stack</span>
    <span class="kw">push</span> ebx               <span class="cmt">; Multiboot info pointer</span>
    <span class="kw">push</span> eax               <span class="cmt">; Multiboot magic</span>
    <span class="kw">call</span> kernel_main       <span class="cmt">; Enter C++ kernel</span>
    <span class="kw">cli</span>
    <span class="kw">hlt</span>                    <span class="cmt">; Halt if kernel returns</span></code></pre>
            </div>
          </div>
        </div>
        <div class="boot-step" data-step="2">
          <div class="boot-step-marker">
            <div class="boot-dot"></div>
            <div class="boot-line"></div>
          </div>
          <div class="boot-step-content">
            <h3>Kernel Initialization</h3>
            <p>The C++ kernel takes over. It sets up the Global Descriptor Table (GDT), Interrupt Descriptor Table (IDT), initializes the physical memory manager and heap allocator, and configures hardware interrupts via the PIC.</p>
            <div class="code-block">
              <div class="code-label">kernel/kernel.cpp</div>
              <pre><code><span class="kw">void</span> kernel_main(<span class="kw">uint32_t</span> magic, multiboot_info* mbi) {
    gdt_init();           <span class="cmt">// Global Descriptor Table</span>
    idt_init();           <span class="cmt">// Interrupt Descriptor Table</span>
    pmm_init(mbi);        <span class="cmt">// Physical memory manager</span>
    heap_init();          <span class="cmt">// Dynamic memory allocation</span>
    pic_remap();          <span class="cmt">// Programmable Interrupt Controller</span>
    keyboard_init();      <span class="cmt">// PS/2 keyboard driver</span>
    mouse_init();         <span class="cmt">// PS/2 mouse driver</span>
    fb_init(mbi);         <span class="cmt">// VESA framebuffer</span>
    <span class="cmt">// ...</span>
}</code></pre>
            </div>
          </div>
        </div>
        <div class="boot-step" data-step="3">
          <div class="boot-step-marker">
            <div class="boot-dot"></div>
            <div class="boot-line"></div>
          </div>
          <div class="boot-step-content">
            <h3>Graphics &amp; Desktop</h3>
            <p>The VESA framebuffer is mapped at 1024&times;768&times;32bpp. The window manager initializes, draws the menubar and dock, and presents the login screen. After authentication, the full desktop environment loads with all 50+ applications ready.</p>
            <div class="code-block">
              <div class="code-label">gui/desktop.cpp</div>
              <pre><code><span class="kw">void</span> desktop_init() {
    wm_init();            <span class="cmt">// Window manager + compositor</span>
    menubar_draw();       <span class="cmt">// Top menu bar with app categories</span>
    dock_draw();          <span class="cmt">// Right-edge pixel-art dock</span>
    login_show();         <span class="cmt">// Three-field login screen</span>
    <span class="cmt">// Desktop ready — event loop begins</span>
}</code></pre>
            </div>
          </div>
        </div>
      </div>
      <div class="boot-nav">
        <button class="boot-prev btn btn-secondary" onclick="bootStep(-1)">Previous</button>
        <div class="boot-indicators"></div>
        <button class="boot-next btn btn-primary" onclick="bootStep(1)">Next</button>
      </div>
    </div>
  </section>

  <!-- Architecture Diagram -->
  <section>
    <div class="container">
      <div class="section-header">
        <span class="overline">Architecture</span>
        <h2>The layer model</h2>
        <p>Click any layer to explore its internals</p>
      </div>
      <div class="arch-diagram">
        <div class="arch-layer" data-layer="apps" onclick="showLayer('apps')">
          <div class="arch-layer-bar">
            <span class="arch-label">Applications</span>
            <span class="arch-detail">50+ apps &middot; 12 source files</span>
          </div>
        </div>
        <div class="arch-layer" data-layer="gui" onclick="showLayer('gui')">
          <div class="arch-layer-bar">
            <span class="arch-label">GUI / Window Manager</span>
            <span class="arch-detail">Compositor &middot; Desktop &middot; Font Rendering</span>
          </div>
        </div>
        <div class="arch-layer" data-layer="services" onclick="showLayer('services')">
          <div class="arch-layer-bar">
            <span class="arch-label">System Services</span>
            <span class="arch-detail">Clipboard &middot; PE32 Loader &middot; Java IDE</span>
          </div>
        </div>
        <div class="arch-layer" data-layer="net" onclick="showLayer('net')">
          <div class="arch-layer-bar">
            <span class="arch-label">Networking</span>
            <span class="arch-detail">TCP/IP &middot; DNS &middot; DHCP &middot; ARP</span>
          </div>
        </div>
        <div class="arch-layer" data-layer="drivers" onclick="showLayer('drivers')">
          <div class="arch-layer-bar">
            <span class="arch-label">Drivers</span>
            <span class="arch-detail">Framebuffer &middot; PS/2 &middot; E1000 &middot; ATA &middot; PCI</span>
          </div>
        </div>
        <div class="arch-layer" data-layer="kernel" onclick="showLayer('kernel')">
          <div class="arch-layer-bar">
            <span class="arch-label">Kernel</span>
            <span class="arch-detail">GDT &middot; IDT &middot; Memory &middot; Interrupts</span>
          </div>
        </div>
        <div class="arch-layer arch-hw">
          <div class="arch-layer-bar">
            <span class="arch-label">x86 Hardware</span>
            <span class="arch-detail">CPU &middot; RAM &middot; NIC &middot; Disk &middot; GPU</span>
          </div>
        </div>
      </div>

      <!-- Layer Detail Panel -->
      <div class="layer-panel" id="layer-panel">
        <button class="layer-close" onclick="hideLayer()">&times;</button>
        <div class="layer-panel-content" id="layer-panel-content"></div>
      </div>
    </div>
  </section>

  <!-- Subsystem Deep Dives -->
  <section class="dark">
    <div class="container">
      <div class="section-header">
        <span class="overline" style="color: var(--burnt-orange);">Deep Dive</span>
        <h2>Subsystem breakdowns</h2>
        <p style="color: var(--clay);">Explore the major subsystems that make up GatewayOS2</p>
      </div>
      <div class="accordion">
        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">Memory Management</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>GatewayOS2 uses a two-tier memory management system. The <strong>Physical Memory Manager (PMM)</strong> tracks available physical pages using a bitmap allocator initialized from the Multiboot memory map. Above that, a <strong>heap allocator</strong> provides dynamic memory allocation (malloc/free) for kernel and application use.</p>
            <p>There is no virtual memory or paging — the system runs entirely in a flat memory model where virtual addresses equal physical addresses. This simplifies the design significantly while trading off process isolation.</p>
            <div class="code-block">
              <div class="code-label">memory/pmm.cpp</div>
              <pre><code><span class="cmt">// Bitmap-based physical page allocator</span>
<span class="kw">static uint32_t</span> bitmap[BITMAP_SIZE];

<span class="kw">void*</span> pmm_alloc_page() {
    <span class="kw">for</span> (<span class="kw">int</span> i = <span class="num">0</span>; i < BITMAP_SIZE; i++) {
        <span class="kw">if</span> (bitmap[i] != <span class="num">0xFFFFFFFF</span>) {
            <span class="kw">int</span> bit = first_free_bit(bitmap[i]);
            bitmap[i] |= (<span class="num">1</span> << bit);
            <span class="kw">return</span> (<span class="kw">void*</span>)((i * <span class="num">32</span> + bit) * PAGE_SIZE);
        }
    }
    <span class="kw">return</span> NULL; <span class="cmt">// Out of memory</span>
}</code></pre>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">Network Stack</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>The networking stack is built from the ground up in seven layers:</p>
            <div class="stack-visual">
              <div class="stack-row"><span class="stack-label">Application</span><span class="stack-desc">Mail client, DNS resolver</span></div>
              <div class="stack-row"><span class="stack-label">TCP</span><span class="stack-desc">Connection state machine, 3-way handshake, retransmission</span></div>
              <div class="stack-row"><span class="stack-label">UDP</span><span class="stack-desc">Connectionless datagrams for DNS queries</span></div>
              <div class="stack-row"><span class="stack-label">IP</span><span class="stack-desc">IPv4 packet routing with header checksum</span></div>
              <div class="stack-row"><span class="stack-label">ARP</span><span class="stack-desc">Address resolution with cache table</span></div>
              <div class="stack-row"><span class="stack-label">Ethernet</span><span class="stack-desc">Frame construction and MAC addressing</span></div>
              <div class="stack-row"><span class="stack-label">E1000 NIC</span><span class="stack-desc">Hardware driver with TX/RX ring buffers</span></div>
            </div>
            <p>DHCP auto-configures the IP address on boot. DNS resolution translates hostnames for the mail client. The TCP implementation handles the full connection lifecycle including SYN/ACK handshakes.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">Window Manager &amp; Compositor</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>The window manager operates on a <strong>single-tasking cooperative model</strong>. One application draws at a time through the compositor. Windows are tracked in a z-ordered list with support for:</p>
            <ul class="guide-list">
              <li>Mouse-driven dragging via title bar</li>
              <li>Focus tracking with visual indicators</li>
              <li>Minimize to dock and restore</li>
              <li>Close button with application cleanup</li>
              <li>Proper z-ordering and overlap handling</li>
            </ul>
            <p>The framebuffer is a linear array of 32-bit pixels (BGRA format) mapped directly from the VESA mode set during boot. All rendering — rectangles, lines, text, images — is done through direct pixel writes to this buffer.</p>
            <div class="code-block">
              <div class="code-label">gui/window.cpp</div>
              <pre><code><span class="kw">struct</span> Window {
    <span class="kw">int</span> x, y, width, height;
    <span class="kw">char</span> title[<span class="num">64</span>];
    <span class="kw">uint32_t</span>* buffer;      <span class="cmt">// Per-window pixel buffer</span>
    <span class="kw">bool</span> focused, minimized;
    <span class="kw">void</span> (*draw)(<span class="kw">void</span>);    <span class="cmt">// Application draw callback</span>
    <span class="kw">void</span> (*on_key)(<span class="kw">int</span>);   <span class="cmt">// Keyboard event handler</span>
    <span class="kw">void</span> (*on_mouse)(MouseEvent); <span class="cmt">// Mouse event handler</span>
};</code></pre>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">PE32 Loader &amp; Win32 Shim</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>GatewayOS2 can load and execute standard Windows PE32 (.exe) executables. The loader parses the PE header, maps sections into memory, resolves imports against a built-in Win32 API compatibility layer, and transfers control to the entry point.</p>
            <p>Over <strong>60 Win32 API functions</strong> are shimmed, including:</p>
            <ul class="guide-list">
              <li><code>MessageBoxA</code>, <code>CreateWindowEx</code> — Window creation</li>
              <li><code>GetDC</code>, <code>TextOut</code>, <code>BitBlt</code> — Graphics operations</li>
              <li><code>malloc</code>, <code>free</code>, <code>memcpy</code> — Memory management</li>
              <li><code>CreateFile</code>, <code>ReadFile</code>, <code>WriteFile</code> — File I/O</li>
              <li><code>GetTickCount</code>, <code>Sleep</code> — Timing functions</li>
            </ul>
            <p>Each shimmed function translates Win32 calls into GatewayOS2 native equivalents, enabling real Windows programs to run on bare metal.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">Cryptography Engine</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>The crypto subsystem provides six algorithms implemented from specification, with no external libraries:</p>
            <div class="crypto-grid">
              <div class="crypto-item">
                <h4>AES-128</h4>
                <p>Symmetric block cipher with key expansion, SubBytes, ShiftRows, MixColumns, and AddRoundKey transformations across 10 rounds.</p>
              </div>
              <div class="crypto-item">
                <h4>SHA-256</h4>
                <p>Cryptographic hash function producing 256-bit digests via 64 rounds of compression with message scheduling.</p>
              </div>
              <div class="crypto-item">
                <h4>RSA</h4>
                <p>Asymmetric encryption with modular exponentiation for key generation, encryption, and digital signatures.</p>
              </div>
              <div class="crypto-item">
                <h4>Caesar / Vigen&egrave;re</h4>
                <p>Classical substitution ciphers for educational demonstration alongside the modern algorithms.</p>
              </div>
              <div class="crypto-item">
                <h4>Base64</h4>
                <p>Binary-to-text encoding used throughout the networking stack for email attachment handling.</p>
              </div>
              <div class="crypto-item">
                <h4>XOR Cipher</h4>
                <p>Symmetric stream cipher using key-derived XOR operations for fast, lightweight encryption.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-trigger" onclick="toggleAccordion(this)">
            <span class="accordion-title">Java IDE &amp; Interpreter</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-content">
            <p>The built-in Java IDE is a complete development environment running on bare metal:</p>
            <ul class="guide-list">
              <li><strong>Editor</strong> — Syntax-aware text editor with Java keyword highlighting</li>
              <li><strong>Interpreter</strong> — Recursive-descent parser supporting variables, if/else, while loops, for loops, arrays, print statements, and basic expressions</li>
              <li><strong>Console</strong> — Output window showing program results and error messages</li>
              <li><strong>Samples</strong> — 8 pre-loaded programs demonstrating language features</li>
            </ul>
            <p>Press <kbd>F5</kbd> to compile and run. The interpreter tokenizes the source, builds an AST, and evaluates it in a single pass. Variables are stored in a hash map with scope support for nested blocks.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- File Structure Explorer -->
  <section>
    <div class="container">
      <div class="section-header">
        <span class="overline">Source Code</span>
        <h2>Project anatomy</h2>
        <p>The complete directory structure of GatewayOS2</p>
      </div>
      <div class="file-tree">
        <div class="file-tree-item folder open" onclick="toggleFolder(this)">
          <span class="file-icon">&#x25BC;</span> GatewayOS2/
        </div>
        <div class="file-tree-children">
          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> boot/
            <span class="file-desc">x86 bootloader (NASM assembly)</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">boot.asm <span class="file-desc">Multiboot entry, stack setup, protected mode</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> kernel/
            <span class="file-desc">Core kernel &amp; system services</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">kernel.cpp <span class="file-desc">Main entry, GDT/IDT, initialization</span></div>
            <div class="file-tree-item file">pe_loader.cpp <span class="file-desc">PE32 executable parser &amp; loader</span></div>
            <div class="file-tree-item file">win32_shim.cpp <span class="file-desc">60+ Win32 API compatibility functions</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> drivers/
            <span class="file-desc">Hardware abstraction layer</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">framebuffer.cpp <span class="file-desc">VESA video driver</span></div>
            <div class="file-tree-item file">keyboard.cpp <span class="file-desc">PS/2 keyboard with scan codes</span></div>
            <div class="file-tree-item file">mouse.cpp <span class="file-desc">PS/2 mouse driver</span></div>
            <div class="file-tree-item file">e1000.cpp <span class="file-desc">Intel E1000 NIC driver</span></div>
            <div class="file-tree-item file">ata.cpp <span class="file-desc">ATA PIO disk driver</span></div>
            <div class="file-tree-item file">pci.cpp <span class="file-desc">PCI bus enumeration</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> gui/
            <span class="file-desc">Graphical user interface</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">window.cpp <span class="file-desc">Window manager &amp; compositor</span></div>
            <div class="file-tree-item file">desktop.cpp <span class="file-desc">Desktop environment &amp; event loop</span></div>
            <div class="file-tree-item file">dock.cpp <span class="file-desc">Application dock (pixel art icons)</span></div>
            <div class="file-tree-item file">menubar.cpp <span class="file-desc">Top menu bar with categories</span></div>
            <div class="file-tree-item file">font.cpp <span class="file-desc">Bitmap font rendering engine</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> apps/
            <span class="file-desc">50+ built-in applications</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">apps_productivity.cpp <span class="file-desc">Editor, calc, calendar, contacts, notes</span></div>
            <div class="file-tree-item file">apps_games.cpp <span class="file-desc">Snake, Pong, Tetris, Chess, Billiards</span></div>
            <div class="file-tree-item file">apps_scifi.cpp <span class="file-desc">Matrix Rain, StarMap, Uplink, Probe</span></div>
            <div class="file-tree-item file">apps_security.cpp <span class="file-desc">Cipher, Fortress, Sentinel, Hashlab</span></div>
            <div class="file-tree-item file">apps_java.cpp <span class="file-desc">Java IDE, interpreter, sample programs</span></div>
            <div class="file-tree-item file">... <span class="file-desc">+7 more application source files</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> net/
            <span class="file-desc">Complete network stack</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">ethernet.cpp <span class="file-desc">Ethernet frame handling</span></div>
            <div class="file-tree-item file">arp.cpp <span class="file-desc">Address Resolution Protocol</span></div>
            <div class="file-tree-item file">ip.cpp <span class="file-desc">IPv4 packet handling</span></div>
            <div class="file-tree-item file">tcp.cpp <span class="file-desc">TCP connection state machine</span></div>
            <div class="file-tree-item file">udp.cpp <span class="file-desc">UDP datagram support</span></div>
            <div class="file-tree-item file">dhcp.cpp <span class="file-desc">Dynamic host configuration</span></div>
            <div class="file-tree-item file">dns.cpp <span class="file-desc">Domain name resolution</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> crypto/
            <span class="file-desc">Cryptographic primitives</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">aes.cpp <span class="file-desc">AES-128 block cipher</span></div>
            <div class="file-tree-item file">sha256.cpp <span class="file-desc">SHA-256 hash function</span></div>
            <div class="file-tree-item file">rsa.cpp <span class="file-desc">RSA asymmetric encryption</span></div>
            <div class="file-tree-item file">base64.cpp <span class="file-desc">Base64 encoding/decoding</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> memory/
            <span class="file-desc">Memory management</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">pmm.cpp <span class="file-desc">Physical page allocator (bitmap)</span></div>
            <div class="file-tree-item file">heap.cpp <span class="file-desc">Dynamic heap allocator</span></div>
          </div>

          <div class="file-tree-item folder" onclick="toggleFolder(this)">
            <span class="file-icon">&#x25B6;</span> lib/
            <span class="file-desc">Runtime utilities</span>
          </div>
          <div class="file-tree-children" style="display:none;">
            <div class="file-tree-item file">string.cpp <span class="file-desc">String operations (no libc)</span></div>
            <div class="file-tree-item file">printf.cpp <span class="file-desc">Custom printf implementation</span></div>
            <div class="file-tree-item file">math.cpp <span class="file-desc">Math functions (no libm)</span></div>
          </div>

          <div class="file-tree-item file">Makefile <span class="file-desc">Cross-compiler build system</span></div>
          <div class="file-tree-item file">linker.ld <span class="file-desc">Kernel memory layout script</span></div>
          <div class="file-tree-item file">grub.cfg <span class="file-desc">GRUB bootloader configuration</span></div>
          <div class="file-tree-item file">mail_relay.py <span class="file-desc">Host-side Gmail relay</span></div>
        </div>
      </div>
    </div>
  </section>

  <section class="teal">
    <div class="pullquote">
      <blockquote>&ldquo;No libc. No libstdc++. No frameworks. Every function in the runtime is hand-written for this freestanding environment.&rdquo;</blockquote>
    </div>
  </section>
