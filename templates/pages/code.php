  <section class="hero" style="padding: 5rem 3rem 3rem;">
    <div class="hero-content">
      <p class="overline">Source Code</p>
      <h1>Read the <em>machine</em></h1>
      <p class="subtitle">Interactive, annotated walkthroughs of the code that makes GatewayOS2 run.</p>
    </div>
  </section>

  <!-- Tabbed Code Explorer -->
  <section class="alt">
    <div class="container">
      <div class="code-tabs">
        <button class="code-tab active" onclick="showCodeTab('bootloader')">Bootloader</button>
        <button class="code-tab" onclick="showCodeTab('kernel')">Kernel</button>
        <button class="code-tab" onclick="showCodeTab('graphics')">Graphics</button>
        <button class="code-tab" onclick="showCodeTab('network')">Networking</button>
        <button class="code-tab" onclick="showCodeTab('pe32')">PE32 Loader</button>
        <button class="code-tab" onclick="showCodeTab('crypto')">Crypto</button>
      </div>

      <div class="code-explorer">
        <!-- Bootloader -->
        <div class="code-panel active" id="panel-bootloader">
          <div class="code-split">
            <div class="code-source">
              <div class="code-block large">
                <div class="code-label">boot/boot.asm &mdash; The first code that runs</div>
                <pre><code><span class="ln"> 1</span>  <span class="cmt">; ============================================</span>
<span class="ln"> 2</span>  <span class="cmt">; GatewayOS2 Multiboot Entry Point</span>
<span class="ln"> 3</span>  <span class="cmt">; ============================================</span>
<span class="ln"> 4</span>
<span class="ln"> 5</span>  <span class="kw">section</span> .multiboot
<span class="ln"> 6</span>  <span class="kw">align</span> <span class="num">4</span>
<span class="ln"> 7</span>      <span class="kw">dd</span> MAGIC        <span class="cmt">; 0x1BADB002</span>
<span class="ln"> 8</span>      <span class="kw">dd</span> FLAGS        <span class="cmt">; Request align + meminfo + video</span>
<span class="ln"> 9</span>      <span class="kw">dd</span> CHECKSUM     <span class="cmt">; -(MAGIC + FLAGS)</span>
<span class="ln">10</span>      <span class="cmt">; Video mode request fields</span>
<span class="ln">11</span>      <span class="kw">dd</span> <span class="num">0</span>, <span class="num">0</span>, <span class="num">0</span>, <span class="num">0</span>, <span class="num">0</span>
<span class="ln">12</span>      <span class="kw">dd</span> <span class="num">0</span>            <span class="cmt">; Linear graphics</span>
<span class="ln">13</span>      <span class="kw">dd</span> <span class="num">1024</span>, <span class="num">768</span>, <span class="num">32</span> <span class="cmt">; 1024x768x32bpp</span>
<span class="ln">14</span>
<span class="ln">15</span>  <span class="kw">section</span> .text
<span class="ln">16</span>  <span class="kw">global</span> _start
<span class="ln">17</span>  <span class="kw">extern</span> kernel_main
<span class="ln">18</span>
<span class="ln">19</span>  _start:
<span class="ln">20</span>      <span class="kw">mov</span>  esp, stack_top
<span class="ln">21</span>      <span class="kw">push</span> ebx
<span class="ln">22</span>      <span class="kw">push</span> eax
<span class="ln">23</span>      <span class="kw">call</span> kernel_main
<span class="ln">24</span>      <span class="kw">cli</span>
<span class="ln">25</span>      <span class="kw">hlt</span>
<span class="ln">26</span>
<span class="ln">27</span>  <span class="kw">section</span> .bss
<span class="ln">28</span>  <span class="kw">align</span> <span class="num">16</span>
<span class="ln">29</span>  stack_bottom:
<span class="ln">30</span>      <span class="kw">resb</span> <span class="num">16384</span>      <span class="cmt">; 16KB stack</span>
<span class="ln">31</span>  stack_top:</code></pre>
              </div>
            </div>
            <div class="code-annotations">
              <div class="annotation">
                <h4>Lines 5-13: Multiboot Header</h4>
                <p>The magic number <code>0x1BADB002</code> tells GRUB this is a valid Multiboot kernel. The flags request memory info and a specific video mode &mdash; 1024&times;768 at 32 bits per pixel.</p>
              </div>
              <div class="annotation">
                <h4>Lines 19-25: Entry Point</h4>
                <p>This is literally the first instruction that runs. It sets up a 16KB stack, pushes the Multiboot info struct pointer, and calls into C++. If <code>kernel_main</code> ever returns, we disable interrupts and halt the CPU.</p>
              </div>
              <div class="annotation">
                <h4>Lines 27-31: Stack</h4>
                <p>16KB of uninitialized memory reserved for the kernel stack. Every function call, local variable, and interrupt handler uses this space. It grows downward from <code>stack_top</code>.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Kernel -->
        <div class="code-panel" id="panel-kernel">
          <div class="code-split">
            <div class="code-source">
              <div class="code-block large">
                <div class="code-label">kernel/kernel.cpp &mdash; System initialization</div>
                <pre><code><span class="ln"> 1</span>  <span class="kw">void</span> kernel_main(<span class="kw">uint32_t</span> magic,
<span class="ln"> 2</span>                   multiboot_info_t* mbi) {
<span class="ln"> 3</span>
<span class="ln"> 4</span>      <span class="cmt">// Core hardware tables</span>
<span class="ln"> 5</span>      gdt_init();         <span class="cmt">// Segment descriptors</span>
<span class="ln"> 6</span>      idt_init();         <span class="cmt">// Interrupt vectors</span>
<span class="ln"> 7</span>      pic_remap();        <span class="cmt">// IRQ routing</span>
<span class="ln"> 8</span>
<span class="ln"> 9</span>      <span class="cmt">// Memory subsystem</span>
<span class="ln">10</span>      pmm_init(mbi);     <span class="cmt">// Physical pages</span>
<span class="ln">11</span>      heap_init();       <span class="cmt">// malloc/free</span>
<span class="ln">12</span>
<span class="ln">13</span>      <span class="cmt">// Input devices</span>
<span class="ln">14</span>      keyboard_init();
<span class="ln">15</span>      mouse_init();
<span class="ln">16</span>
<span class="ln">17</span>      <span class="cmt">// Display</span>
<span class="ln">18</span>      fb_init(mbi);      <span class="cmt">// VESA framebuffer</span>
<span class="ln">19</span>
<span class="ln">20</span>      <span class="cmt">// Storage & networking</span>
<span class="ln">21</span>      pci_scan();        <span class="cmt">// Find PCI devices</span>
<span class="ln">22</span>      ata_init();        <span class="cmt">// Disk driver</span>
<span class="ln">23</span>      e1000_init();      <span class="cmt">// Network card</span>
<span class="ln">24</span>      dhcp_discover();   <span class="cmt">// Get IP address</span>
<span class="ln">25</span>
<span class="ln">26</span>      <span class="cmt">// Launch desktop</span>
<span class="ln">27</span>      desktop_run();     <span class="cmt">// Never returns</span>
<span class="ln">28</span>  }</code></pre>
              </div>
            </div>
            <div class="code-annotations">
              <div class="annotation">
                <h4>Lines 5-7: CPU Tables</h4>
                <p>The GDT defines memory segments (code, data, stack) for protected mode. The IDT maps hardware interrupts and CPU exceptions to handler functions. The PIC remaps IRQ lines to avoid conflicts with CPU exception vectors.</p>
              </div>
              <div class="annotation">
                <h4>Lines 10-11: Memory</h4>
                <p>The PMM reads the Multiboot memory map to know which physical pages are available. The heap allocator sits on top, providing <code>malloc()</code> and <code>free()</code> for dynamic allocation &mdash; all without libc.</p>
              </div>
              <div class="annotation">
                <h4>Lines 21-24: Hardware Discovery</h4>
                <p>PCI bus enumeration finds the E1000 NIC and ATA controller. Once found, drivers initialize the hardware, and DHCP runs to auto-configure networking &mdash; the OS is online before the desktop even loads.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Graphics -->
        <div class="code-panel" id="panel-graphics">
          <div class="code-split">
            <div class="code-source">
              <div class="code-block large">
                <div class="code-label">drivers/framebuffer.cpp &mdash; Raw pixel rendering</div>
                <pre><code><span class="ln"> 1</span>  <span class="kw">static uint32_t</span>* framebuffer;
<span class="ln"> 2</span>  <span class="kw">static int</span> fb_width, fb_height, fb_pitch;
<span class="ln"> 3</span>
<span class="ln"> 4</span>  <span class="kw">void</span> fb_init(multiboot_info_t* mbi) {
<span class="ln"> 5</span>      framebuffer = (<span class="kw">uint32_t</span>*)mbi->framebuffer_addr;
<span class="ln"> 6</span>      fb_width  = mbi->framebuffer_width;   <span class="cmt">// 1024</span>
<span class="ln"> 7</span>      fb_height = mbi->framebuffer_height;  <span class="cmt">// 768</span>
<span class="ln"> 8</span>      fb_pitch  = mbi->framebuffer_pitch;
<span class="ln"> 9</span>  }
<span class="ln">10</span>
<span class="ln">11</span>  <span class="kw">inline void</span> put_pixel(<span class="kw">int</span> x, <span class="kw">int</span> y,
<span class="ln">12</span>                        <span class="kw">uint32_t</span> color) {
<span class="ln">13</span>      <span class="kw">if</span> (x >= <span class="num">0</span> && x < fb_width &&
<span class="ln">14</span>          y >= <span class="num">0</span> && y < fb_height)
<span class="ln">15</span>          framebuffer[y * (fb_pitch/<span class="num">4</span>) + x] = color;
<span class="ln">16</span>  }
<span class="ln">17</span>
<span class="ln">18</span>  <span class="kw">void</span> fill_rect(<span class="kw">int</span> x, <span class="kw">int</span> y,
<span class="ln">19</span>                 <span class="kw">int</span> w, <span class="kw">int</span> h, <span class="kw">uint32_t</span> c) {
<span class="ln">20</span>      <span class="kw">for</span> (<span class="kw">int</span> j = y; j < y+h; j++)
<span class="ln">21</span>          <span class="kw">for</span> (<span class="kw">int</span> i = x; i < x+w; i++)
<span class="ln">22</span>              put_pixel(i, j, c);
<span class="ln">23</span>  }</code></pre>
              </div>
            </div>
            <div class="code-annotations">
              <div class="annotation">
                <h4>Lines 1-2: The Framebuffer</h4>
                <p>A flat array of 32-bit pixels mapped to video memory by the VESA BIOS. Every pixel on screen is a direct memory write &mdash; no GPU abstraction, no graphics API. 1024 &times; 768 = 786,432 pixels &times; 4 bytes = ~3MB of video RAM.</p>
              </div>
              <div class="annotation">
                <h4>Lines 11-16: put_pixel</h4>
                <p>The most fundamental graphics operation. Every rectangle, line, character, window border, and icon ultimately calls this function. Bounds checking prevents writes outside video memory &mdash; critical when there's no memory protection.</p>
              </div>
              <div class="annotation">
                <h4>Lines 18-23: fill_rect</h4>
                <p>Window backgrounds, buttons, title bars, menu items &mdash; all built from filled rectangles. This simple nested loop is the building block for the entire GUI.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Network -->
        <div class="code-panel" id="panel-network">
          <div class="code-split">
            <div class="code-source">
              <div class="code-block large">
                <div class="code-label">net/tcp.cpp &mdash; TCP connection handshake</div>
                <pre><code><span class="ln"> 1</span>  <span class="kw">enum</span> TCPState {
<span class="ln"> 2</span>      CLOSED, LISTEN, SYN_SENT,
<span class="ln"> 3</span>      SYN_RECEIVED, ESTABLISHED,
<span class="ln"> 4</span>      FIN_WAIT_1, FIN_WAIT_2,
<span class="ln"> 5</span>      CLOSE_WAIT, LAST_ACK, TIME_WAIT
<span class="ln"> 6</span>  };
<span class="ln"> 7</span>
<span class="ln"> 8</span>  <span class="kw">void</span> tcp_connect(<span class="kw">uint32_t</span> dest_ip,
<span class="ln"> 9</span>                   <span class="kw">uint16_t</span> dest_port) {
<span class="ln">10</span>
<span class="ln">11</span>      <span class="cmt">// Step 1: Send SYN</span>
<span class="ln">12</span>      tcp_send(dest_ip, dest_port,
<span class="ln">13</span>               SYN, seq_num, <span class="num">0</span>, NULL, <span class="num">0</span>);
<span class="ln">14</span>      state = SYN_SENT;
<span class="ln">15</span>
<span class="ln">16</span>      <span class="cmt">// Step 2: Wait for SYN+ACK</span>
<span class="ln">17</span>      <span class="kw">while</span> (state == SYN_SENT)
<span class="ln">18</span>          net_poll();
<span class="ln">19</span>
<span class="ln">20</span>      <span class="cmt">// Step 3: Send ACK — connected</span>
<span class="ln">21</span>      tcp_send(dest_ip, dest_port,
<span class="ln">22</span>               ACK, seq_num, ack_num,
<span class="ln">23</span>               NULL, <span class="num">0</span>);
<span class="ln">24</span>      state = ESTABLISHED;
<span class="ln">25</span>  }</code></pre>
              </div>
            </div>
            <div class="code-annotations">
              <div class="annotation">
                <h4>Lines 1-6: State Machine</h4>
                <p>The full TCP state machine, implemented from RFC 793. Every TCP connection transitions through these states &mdash; from the initial handshake through data transfer to graceful shutdown.</p>
              </div>
              <div class="annotation">
                <h4>Lines 11-24: Three-Way Handshake</h4>
                <p>The classic SYN &rarr; SYN+ACK &rarr; ACK sequence that establishes every TCP connection on the internet. This code runs directly on the E1000 NIC hardware &mdash; no OS socket layer, no kernel bypass. GatewayOS2 <em>is</em> the kernel.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- PE32 -->
        <div class="code-panel" id="panel-pe32">
          <div class="code-split">
            <div class="code-source">
              <div class="code-block large">
                <div class="code-label">kernel/pe_loader.cpp &mdash; Windows executable loading</div>
                <pre><code><span class="ln"> 1</span>  <span class="kw">bool</span> pe_load(<span class="kw">uint8_t</span>* exe_data) {
<span class="ln"> 2</span>
<span class="ln"> 3</span>      <span class="cmt">// Parse DOS header</span>
<span class="ln"> 4</span>      DOS_Header* dos = (DOS_Header*)exe_data;
<span class="ln"> 5</span>      <span class="kw">if</span> (dos->e_magic != <span class="num">0x5A4D</span>) <span class="cmt">// "MZ"</span>
<span class="ln"> 6</span>          <span class="kw">return false</span>;
<span class="ln"> 7</span>
<span class="ln"> 8</span>      <span class="cmt">// Parse PE header</span>
<span class="ln"> 9</span>      PE_Header* pe = (PE_Header*)
<span class="ln">10</span>          (exe_data + dos->e_lfanew);
<span class="ln">11</span>      <span class="kw">if</span> (pe->signature != <span class="num">0x4550</span>) <span class="cmt">// "PE"</span>
<span class="ln">12</span>          <span class="kw">return false</span>;
<span class="ln">13</span>
<span class="ln">14</span>      <span class="cmt">// Map sections into memory</span>
<span class="ln">15</span>      <span class="kw">for</span> (<span class="kw">int</span> i = <span class="num">0</span>; i < pe->num_sections; i++)
<span class="ln">16</span>          map_section(&sections[i]);
<span class="ln">17</span>
<span class="ln">18</span>      <span class="cmt">// Resolve Win32 API imports</span>
<span class="ln">19</span>      resolve_imports(pe->import_table);
<span class="ln">20</span>
<span class="ln">21</span>      <span class="cmt">// Jump to entry point</span>
<span class="ln">22</span>      EntryFunc entry = (EntryFunc)
<span class="ln">23</span>          (base + pe->entry_point);
<span class="ln">24</span>      entry();
<span class="ln">25</span>  }</code></pre>
              </div>
            </div>
            <div class="code-annotations">
              <div class="annotation">
                <h4>Lines 3-12: Header Parsing</h4>
                <p>Every Windows .exe starts with a DOS header (the "MZ" magic bytes from 1981) followed by the PE header. The loader validates both signatures before proceeding &mdash; reject anything that isn't a valid PE32 binary.</p>
              </div>
              <div class="annotation">
                <h4>Lines 14-19: Loading</h4>
                <p>PE sections (.text, .data, .rdata) are mapped into memory at their virtual addresses. Then the import table is walked &mdash; each Win32 API call (like <code>MessageBoxA</code>) is resolved to a GatewayOS2 shim function.</p>
              </div>
              <div class="annotation">
                <h4>Lines 21-24: Execution</h4>
                <p>The entry point address is cast to a function pointer and called directly. The Windows program now runs on GatewayOS2, calling shimmed Win32 functions that translate to native OS operations.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Crypto -->
        <div class="code-panel" id="panel-crypto">
          <div class="code-split">
            <div class="code-source">
              <div class="code-block large">
                <div class="code-label">crypto/aes.cpp &mdash; AES-128 encryption</div>
                <pre><code><span class="ln"> 1</span>  <span class="cmt">// AES-128: 10 rounds of transformation</span>
<span class="ln"> 2</span>  <span class="kw">void</span> aes_encrypt(<span class="kw">uint8_t</span>* block,
<span class="ln"> 3</span>                   <span class="kw">uint8_t</span>* key) {
<span class="ln"> 4</span>
<span class="ln"> 5</span>      <span class="kw">uint8_t</span> round_keys[<span class="num">176</span>];
<span class="ln"> 6</span>      key_expansion(key, round_keys);
<span class="ln"> 7</span>
<span class="ln"> 8</span>      add_round_key(block, round_keys);
<span class="ln"> 9</span>
<span class="ln">10</span>      <span class="kw">for</span> (<span class="kw">int</span> r = <span class="num">1</span>; r < <span class="num">10</span>; r++) {
<span class="ln">11</span>          sub_bytes(block);     <span class="cmt">// S-box substitution</span>
<span class="ln">12</span>          shift_rows(block);    <span class="cmt">// Row rotation</span>
<span class="ln">13</span>          mix_columns(block);   <span class="cmt">// Column mixing</span>
<span class="ln">14</span>          add_round_key(block,
<span class="ln">15</span>              round_keys + r*<span class="num">16</span>);
<span class="ln">16</span>      }
<span class="ln">17</span>
<span class="ln">18</span>      <span class="cmt">// Final round (no MixColumns)</span>
<span class="ln">19</span>      sub_bytes(block);
<span class="ln">20</span>      shift_rows(block);
<span class="ln">21</span>      add_round_key(block,
<span class="ln">22</span>          round_keys + <span class="num">160</span>);
<span class="ln">23</span>  }</code></pre>
              </div>
            </div>
            <div class="code-annotations">
              <div class="annotation">
                <h4>Lines 5-6: Key Expansion</h4>
                <p>The 128-bit key is expanded into 11 round keys (176 bytes) using the Rijndael key schedule. Each round uses a different derived key, making the cipher resistant to cryptanalysis.</p>
              </div>
              <div class="annotation">
                <h4>Lines 10-16: The Core Loop</h4>
                <p>Nine rounds of four transformations: <strong>SubBytes</strong> (non-linear S-box substitution), <strong>ShiftRows</strong> (byte rotation), <strong>MixColumns</strong> (Galois field multiplication), and <strong>AddRoundKey</strong> (XOR with round key). This is AES as defined in FIPS 197.</p>
              </div>
              <div class="annotation">
                <h4>Lines 18-22: Final Round</h4>
                <p>The last round omits MixColumns &mdash; a deliberate design choice in the AES spec that makes the cipher invertible for decryption while maintaining full security.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="teal">
    <div class="pullquote">
      <blockquote>&ldquo;The best documentation is the code itself. Every function tells a story about how machines really work.&rdquo;</blockquote>
    </div>
  </section>
