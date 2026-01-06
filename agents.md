# **Project Specification: MIRAI Tournament Hub**

## **1\. Project Overview**

Nama Aplikasi: MIRAI Tournament Hub  
Tipe: SaaS / Platform Manajemen Turnamen Esports  
Deskripsi: Aplikasi berbasis web untuk membuat, mengelola, dan mempublikasikan turnamen esports.  
Target Output: Porting total dari prototype React (dilampirkan di bawah) ke ekosistem Laravel Livewire dengan UI/UX yang identik.

## **2\. Tech Stack (Mandatory \- Laravel 12\)**

Developer wajib menggunakan teknologi berikut:

* **Language:** PHP 8.4+  
* **Framework:** **Laravel 12.x**  
* **Fullstack Framework:** **Livewire 3.x** (Class-based components).  
* **Frontend Logic:** **Alpine.js 3.x**  
* **Styling:** **Tailwind CSS 4.0** (Tanpa library UI berat. Gunakan class utility murni).  
* **Database:** **PostgreSQL** (Wajib fitur JSONB column).  
* **Icons:** **Lucide Icons** (Blade Icons).

## **3\. Database Schema (PostgreSQL)**

### **A. tournaments**

Schema::create('tournaments', function (Blueprint $table) {  
    $table-\>id();  
    $table-\>foreignId('user\_id')-\>constrained();  
    $table-\>string('title');  
    $table-\>string('slug')-\>unique();  
    $table-\>string('game\_type');  
    $table-\>dateTime('event\_date');  
    $table-\>decimal('fee', 12, 2)-\>default(0);  
    $table-\>integer('max\_slots')-\>default(32);  
    $table-\>text('description')-\>nullable();  
    $table-\>string('status')-\>default('open');  
    $table-\>jsonb('form\_schema')-\>nullable(); // Simpan struktur form  
    $table-\>timestamps();  
});

### **B. participants**

Schema::create('participants', function (Blueprint $table) {  
    $table-\>id();  
    $table-\>foreignId('tournament\_id')-\>constrained()-\>cascadeOnDelete();  
    $table-\>string('team\_name');  
    $table-\>string('captain\_name');  
    $table-\>string('whatsapp');  
    $table-\>jsonb('submission\_data')-\>nullable(); // Simpan jawaban  
    $table-\>string('payment\_status')-\>default('pending');   
    $table-\>timestamp('registered\_at');  
    $table-\>timestamps();  
});

## **4\. UI/UX Guidelines (Strict Visual Porting)**

Tujuan utama adalah **REPLIKASI TOTAL** tampilan dari prototype React. **JANGAN BUAT DESAIN SENDIRI.**

**Wajib Tiru Style Ini (Dark Mode Neon):**

* **Global Background:** bg-\[\#050505\] (Hitam pekat).  
* **Card Background:** bg-\[\#1a1a1a\] dengan border border-gray-800.  
* **Accent Color:** cyan-500 (Text/Border) dan bg-cyan-600 (Button).  
* **Input:** bg-\[\#222\] atau bg-black dengan border border-gray-700.  
* **Layout:** Form di kiri (lebar), Sidebar Menu di kanan (sticky).

## **5\. Implementation Rules (Laravel Way)**

1. **State Management:** Terjemahkan useState React menjadi Property Livewire (public $fields \= \[\]).  
2. **File Upload:**  
   * Di React menggunakan Base64.  
   * Di Laravel **WAJIB** menggunakan Livewire\\WithFileUploads. Simpan file ke storage/public dan simpan *path*\-nya di JSON.  
3. **Routing:** Gunakan Route Model Binding untuk slug turnamen.

## **6\. Prompt Instructions for AI Agent**

Jika Anda adalah AI Coding Agent, ikuti langkah ini:

1. **Analisis Section 8:** Baca kode React di bawah secara mendalam. Pahami struktur data customFields, logika addField, updateField, dan tampilan JSX-nya.  
2. **Database:** Buat migration sesuai schema di atas.  
3. **Porting ke Livewire:**  
   * Buat komponen CreateTournament (Admin) dan PublicRegistration (User).  
   * Tulis ulang logika JavaScript (React) menjadi PHP (Livewire Class).  
   * Tulis ulang JSX menjadi Blade Template dengan class Tailwind yang **SAMA PERSIS**.

## **7\. SOURCE OF TRUTH (REACT CODE REFERENCE)**

**Gunakan kode ini sebagai referensi logika dan tampilan utama:**

// React Component Reference for Logic & UI Layout  
const TournamentForm \= ({ onSubmit, initialData, isEdit, onCancel }) \=\> {  
  // STATE STRUCTURE EXAMPLE  
  // Translate this to Livewire Public Properties  
  const \[form, setForm\] \= useState({  
    title: '', game: 'Mobile Legends', date: '', fee: '', slot: 32, description: ''  
  });

  const \[customFields, setCustomFields\] \= useState(\[  
    {   
      id: 1,   
      label: 'Bukti Transfer',   
      type: 'file',   
      required: true,   
      description: 'Upload screenshot...',   
      // Media Embedder Logic  
      mediaType: 'none', mediaUrl: '', mediaCaption: '',  
      // File Settings Logic  
      fileSettings: { allowedTypes: \['image', 'pdf'\], maxSize: 1048576, restrictTypes: false }  
    },  
  \]);

  // ACTIONS TO PORT TO LIVEWIRE METHODS  
  const addField \= (type, label) \=\> { ... };  
  const updateField \= (id, key, value) \=\> { ... };  
  const removeField \= (id) \=\> { ... };  
  const duplicateField \= (field) \=\> { ... };

  // JSX UI STRUCTURE (PORT TO BLADE)  
  return (  
    \<div className="flex flex-col md:flex-row gap-6 items-start relative"\>  
        
      {/\* LEFT COLUMN: MAIN FORM \*/}  
      \<form className="flex-1 space-y-6 w-full"\>  
          
        {/\* HEADER CONFIG CARD \*/}  
        \<div className="bg-\[\#1a1a1a\] p-6 rounded-xl border-t-8 border-cyan-500 shadow-xl space-y-4"\>  
          \<input className="w-full bg-transparent border-b border-gray-700 text-3xl font-bold text-white..." placeholder="Judul Turnamen"/\>  
          \<textarea className="w-full bg-transparent..." placeholder="Deskripsi..."/\>  
          {/\* Grid inputs for Game, Date, Fee, Slot \*/}  
        \</div\>

        {/\* DYNAMIC FIELDS LOOP \*/}  
        \<div className="space-y-4 pb-20"\>  
          {customFields.map((field) \=\> (  
            \<div className="bg-\[\#1a1a1a\] p-5 rounded-xl border-l-4 border-cyan-500 shadow-lg group relative"\>  
                
              {/\* Question Input Row \*/}  
              \<div className="flex flex-col md:flex-row gap-4 mb-4"\>  
                 \<input className="w-full bg-\[\#222\] border-b border-gray-700 p-3..." value={field.label} /\>  
                 \<select className="w-full bg-\[\#222\]..." value={field.type}\>...\</select\>  
              \</div\>

              {/\* Media Embedder Toolbar \*/}  
              \<div className="flex items-center gap-3 bg-\[\#111\] p-2 rounded border border-gray-800"\>  
                 \<button onClick={() \=\> updateField('mediaType', 'image')}\>\<ImageIcon/\>\</button\>  
                 \<button onClick={() \=\> updateField('mediaType', 'link')}\>\<LinkIcon/\>\</button\>  
                 {/\* Image Upload Input Logic Here \*/}  
              \</div\>

              {/\* Footer Actions \*/}  
              \<div className="flex justify-end pt-4 border-t border-gray-800"\>  
                 \<label\>Wajib Diisi \<input type="checkbox"/\>\</label\>  
                 \<button\>Hapus\</button\>  
              \</div\>

            \</div\>  
          ))}  
        \</div\>  
      \</form\>

      {/\* RIGHT COLUMN: FLOATING SIDEBAR \*/}  
      \<div className="sticky top-24 bg-\[\#1a1a1a\] border border-gray-700 rounded-lg shadow-2xl p-2 flex flex-col gap-3 hidden md:flex"\>  
        \<button onClick={() \=\> addField('text')} className="p-2 hover:text-cyan-400"\>\<Plus/\>\</button\>  
        \<button onClick={() \=\> addField('note')} className="p-2 hover:text-cyan-400"\>\<Type/\>\</button\>  
        \<button onClick={() \=\> addField('image-view')} className="p-2 hover:text-cyan-400"\>\<ImageIcon/\>\</button\>  
      \</div\>

    \</div\>  
  );  
};  
