import React, { useState, useEffect, useRef } from 'react';
import { initializeApp } from 'firebase/app';
import { 
  getFirestore, collection, addDoc, onSnapshot, 
  query, orderBy, doc, updateDoc, deleteDoc, serverTimestamp, getDoc 
} from 'firebase/firestore';
import { getAuth, signInAnonymously, onAuthStateChanged, signInWithCustomToken } from 'firebase/auth';
import { 
  Trophy, Users, Calendar, DollarSign, 
  Gamepad2, Plus, ArrowLeft, Share2, 
  Trash2, CheckCircle, Copy, ExternalLink,
  ChevronRight, Activity, Settings, List, Type, Hash, FileText, Image as ImageIcon,
  CheckSquare, Circle, ArrowDownCircle, X, AlignLeft, Clock, Link as LinkIcon, MessageSquare, AlertCircle,
  Edit2, RefreshCw, MoreVertical, Layout, UploadCloud, Video, Music, File, Download, UserCheck, Lock, Unlock
} from 'lucide-react';

// --- FIREBASE SETUP ---
const firebaseConfig = JSON.parse(__firebase_config || '{}'); 
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const appId = typeof __app_id !== 'undefined' ? __app_id : 'mirai-default';

// --- STYLES ---
const customStyles = `
  input[type="date"]::-webkit-calendar-picker-indicator,
  input[type="datetime-local"]::-webkit-calendar-picker-indicator,
  input[type="time"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: 0.6;
    transition: 0.2s;
  }
  input[type="date"]::-webkit-calendar-picker-indicator:hover,
  input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover,
  input[type="time"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
  }
  @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  .animate-fade-in { animation: slideIn 0.3s ease-out; }
  
  /* Toggle Switch */
  .toggle-checkbox:checked {
    right: 0;
    border-color: #06b6d4;
  }
  .toggle-checkbox:checked + .toggle-label {
    background-color: #06b6d4;
  }
`;

// --- HELPERS ---
const formatDate = (dateString) => {
  if (!dateString) return '-';
  try {
    return new Date(dateString).toLocaleString('id-ID', { 
      weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' 
    });
  } catch (e) { return dateString; }
};

// --- MAIN APP ---
export default function App() {
  const [user, setUser] = useState(null);
  const [view, setView] = useState('dashboard');
  const [tournaments, setTournaments] = useState([]);
  const [selectedTournament, setSelectedTournament] = useState(null);
  const [loading, setLoading] = useState(true);
  const [toast, setToast] = useState({ message: '', type: '' });

  // Auth & Data Fetching
  useEffect(() => {
    const init = async () => {
        try {
            if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) await signInWithCustomToken(auth, __initial_auth_token);
            else await signInAnonymously(auth);
        } catch (e) { console.error(e); }
    };
    init();
    return onAuthStateChanged(auth, setUser);
  }, []);

  useEffect(() => {
    if (!user) return;
    setLoading(true);
    const q = collection(db, 'artifacts', appId, 'public', 'data', 'tournaments');
    return onSnapshot(q, (snapshot) => {
      const data = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      data.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));
      setTournaments(data);
      setLoading(false);
    });
  }, [user]);

  const showToast = (msg, type = 'success') => { setToast({ message: msg, type }); setTimeout(() => setToast({ message: '', type: '' }), 3000); };

  const handleCreate = async (formData) => {
    if (!user) return;
    try {
      await addDoc(collection(db, 'artifacts', appId, 'public', 'data', 'tournaments'), {
        ...formData, status: 'open', registrantsCount: 0, createdAt: serverTimestamp(), createdBy: user.uid
      });
      showToast('Turnamen berhasil dibuat!', 'success');
      setView('dashboard');
    } catch (e) { showToast('Gagal membuat', 'error'); }
  };

  const handleUpdate = async (formData) => {
    if (!user || !selectedTournament) return;
    try {
      await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'tournaments', selectedTournament.id), {
        ...formData, updatedAt: serverTimestamp()
      });
      showToast('Turnamen diperbarui!', 'success');
      setView('dashboard'); setSelectedTournament(null);
    } catch (e) { showToast('Gagal update', 'error'); }
  };

  const toggleStatus = async (id, currentStatus) => {
    try {
        const newStatus = currentStatus === 'open' ? 'closed' : 'open';
        await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'tournaments', id), {
            status: newStatus
        });
        showToast(`Turnamen ${newStatus === 'open' ? 'Dibuka' : 'Ditutup'}!`, 'success');
    } catch (e) { showToast('Gagal mengubah status', 'error'); }
  };

  const handleDelete = async (id) => {
    if(!confirm("Hapus turnamen ini?")) return;
    try {
      await deleteDoc(doc(db, 'artifacts', appId, 'public', 'data', 'tournaments', id));
      showToast('Terhapus', 'success');
    } catch (e) { showToast('Gagal hapus', 'error'); }
  };

  const handleRegister = async (tId, data) => {
    if (!user) return false;
    try {
      await addDoc(collection(db, 'artifacts', appId, 'public', 'data', `participants_${tId}`), {
        ...data, status: 'pending', registeredAt: serverTimestamp()
      });
      return true;
    } catch (e) { return false; }
  };

  if (loading && !tournaments.length && view === 'dashboard') return <div className="bg-[#050505] min-h-screen flex items-center justify-center text-white">Loading...</div>;

  return (
    <div className="min-h-screen bg-[#050505] text-white font-sans selection:bg-cyan-500 selection:text-black pb-20 md:pb-0">
      <style>{customStyles}</style>
      <nav className="border-b border-white/10 bg-[#0a0a0a]/80 backdrop-blur sticky top-0 z-40">
        <div className="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
          <div className="flex items-center gap-2 cursor-pointer" onClick={() => setView('dashboard')}>
            <div className="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded flex items-center justify-center font-bold font-mono">M</div>
            <span className="font-bold tracking-tight text-lg">MIRAI <span className="text-cyan-400">HUB</span></span>
          </div>
          <div className="text-xs text-gray-500">{view === 'public-form' ? 'Public View' : 'Admin Mode'}</div>
        </div>
      </nav>

      <main className="max-w-6xl mx-auto px-4 py-8">
        {view === 'dashboard' && (
          <div className="space-y-8 animate-fade-in">
            <div className="flex justify-between items-center">
              <h1 className="text-2xl font-bold">Dashboard</h1>
              <button onClick={() => { setSelectedTournament(null); setView('create'); }} className="bg-cyan-600 hover:bg-cyan-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition">
                <Plus size={18} /> Baru
              </button>
            </div>

            {tournaments.length === 0 ? (
              <div className="text-center py-20 border border-dashed border-gray-800 rounded-2xl">
                <Gamepad2 size={48} className="mx-auto text-gray-700 mb-4" />
                <p className="text-gray-500">Belum ada turnamen aktif.</p>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {tournaments.map((t) => (
                  <div key={t.id} className={`bg-[#111] border rounded-xl p-5 transition group relative ${t.status === 'closed' ? 'border-red-900/50 opacity-75' : 'border-gray-800 hover:border-cyan-500/50'}`}>
                    <div className="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition z-10">
                      <button onClick={(e) => { e.stopPropagation(); setSelectedTournament(t); setView('edit'); }} className="text-gray-400 hover:text-white bg-black/80 p-2 rounded"><Edit2 size={14} /></button>
                      <button onClick={(e) => { e.stopPropagation(); handleDelete(t.id); }} className="text-red-500 hover:text-red-400 bg-black/80 p-2 rounded"><Trash2 size={14} /></button>
                    </div>
                    <div className="mb-3 flex justify-between items-start">
                        <span className="text-[10px] font-bold tracking-widest uppercase text-cyan-500 bg-cyan-950/30 px-2 py-1 rounded border border-cyan-900/50">{t.game}</span>
                        <div onClick={(e) => {e.stopPropagation(); toggleStatus(t.id, t.status)}} className={`cursor-pointer px-2 py-1 rounded-full text-[10px] font-bold uppercase flex items-center gap-1 ${t.status === 'closed' ? 'bg-red-900/30 text-red-500 border border-red-900' : 'bg-green-900/30 text-green-500 border border-green-900'}`}>
                            {t.status === 'closed' ? <Lock size={10}/> : <Unlock size={10}/>}
                            {t.status === 'closed' ? 'Closed' : 'Open'}
                        </div>
                    </div>
                    <h3 className="text-lg font-bold mb-1 truncate">{t.title}</h3>
                    <div className="text-xs text-gray-400 mb-4 space-y-1">
                      <div className="flex items-center gap-2"><Calendar size={12} />{formatDate(t.date)}</div>
                      <div className="flex items-center gap-2"><DollarSign size={12} />{t.fee === '0' ? 'Free' : `Rp ${parseInt(t.fee).toLocaleString()}`}</div>
                    </div>
                    <div className="grid grid-cols-2 gap-2">
                      <button onClick={() => { setSelectedTournament(t); setView('details'); }} className="bg-gray-800 hover:bg-gray-700 text-white py-2 rounded text-xs font-bold transition">Data</button>
                      <button onClick={() => { setSelectedTournament(t); setView('public-form'); }} className="border border-gray-700 hover:border-cyan-500 text-gray-300 hover:text-cyan-400 py-2 rounded text-xs font-bold transition flex justify-center items-center gap-1">Form <ExternalLink size={12}/></button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {(view === 'create' || view === 'edit') && (
          <TournamentForm 
            onSubmit={view === 'create' ? handleCreate : handleUpdate} 
            initialData={view === 'edit' ? selectedTournament : null} 
            isEdit={view === 'edit'}
            onCancel={() => setView('dashboard')}
          />
        )}

        {view === 'details' && selectedTournament && (
          <TournamentDetails 
            tournament={selectedTournament} 
            onBack={() => { setSelectedTournament(null); setView('dashboard'); }}
            db={db} appId={appId} user={user}
            showToast={showToast}
          />
        )}

        {view === 'public-form' && selectedTournament && (
          <PublicRegistration 
            tournament={selectedTournament} 
            onSubmit={handleRegister}
            onBack={() => { setSelectedTournament(null); setView('dashboard'); }} 
          />
        )}
      </main>
      
      {toast.message && (
        <div className={`fixed bottom-4 right-4 ${toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-fade-in`}>
          {toast.type === 'success' ? <CheckCircle size={20} /> : <AlertCircle size={20} />}
          <span className="font-medium">{toast.message}</span>
        </div>
      )}
    </div>
  );
}

// --- FORM EDITOR COMPONENT ---

const TournamentForm = ({ onSubmit, initialData, isEdit, onCancel }) => {
  const [form, setForm] = useState({
    title: '', game: 'Mobile Legends', date: '', fee: '', slot: 32, description: ''
  });

  const [customFields, setCustomFields] = useState([
    { 
      id: Date.now(), 
      label: 'Bukti Transfer', 
      type: 'file', 
      required: true, 
      description: 'Upload screenshot transfer sesuai nominal.', 
      fileSettings: { allowedTypes: ['image', 'pdf'], maxSize: 1048576, restrictTypes: false },
      // Separate properties for Image and Link
      imageUrl: '', 
      linkUrl: '', 
      linkText: '',
      showImageInput: false,
      showLinkInput: false
    },
  ]);

  useEffect(() => {
    if (initialData) {
      setForm({
        title: initialData.title || '', game: initialData.game || 'Mobile Legends',
        date: initialData.date || '', fee: initialData.fee || '', slot: initialData.slot || 32,
        description: initialData.description || '' 
      });
      if (initialData.formSchema) {
        const loaded = initialData.formSchema.filter(f => !f.isFixed);
        if (loaded.length > 0) setCustomFields(loaded);
      }
    }
  }, [initialData]);

  // Image Upload Handler for Admin/Builder
  const handleBuilderImageUpload = (e, onSuccess) => {
    const file = e.target.files[0];
    if(!file) return;
    if(file.size > 800 * 1024) { 
        alert("Ukuran file terlalu besar! Maksimal 800KB.");
        e.target.value = null;
        return;
    }
    const reader = new FileReader();
    reader.onloadend = () => onSuccess(reader.result);
    reader.readAsDataURL(file);
  };

  // Actions
  const addField = (type = 'text', label = 'Pertanyaan Baru') => {
    setCustomFields([...customFields, { 
      id: Date.now(), label, type, required: false, options: ['Opsi 1'], description: '',
      fileSettings: { allowedTypes: ['image'], maxSize: 1048576, restrictTypes: false },
      imageUrl: '', linkUrl: '', linkText: '', showImageInput: false, showLinkInput: false
    }]);
  };

  const updateField = (id, key, value) => {
    setCustomFields(customFields.map(f => f.id === id ? { ...f, [key]: value } : f));
  };

  const updateFileSettings = (id, key, value) => {
    setCustomFields(customFields.map(f => {
        if(f.id === id) {
            return { ...f, fileSettings: { ...f.fileSettings, [key]: value } };
        }
        return f;
    }));
  };

  const toggleFileType = (id, type) => {
    const field = customFields.find(f => f.id === id);
    if (!field) return;
    const currentTypes = field.fileSettings.allowedTypes || [];
    let newTypes;
    if (currentTypes.includes(type)) {
        newTypes = currentTypes.filter(t => t !== type);
    } else {
        newTypes = [...currentTypes, type];
    }
    updateFileSettings(id, 'allowedTypes', newTypes);
  };

  const removeField = (id) => setCustomFields(customFields.filter(f => f.id !== id));
  const duplicateField = (field) => setCustomFields([...customFields, { ...field, id: Date.now() }]);

  // Options Logic
  const handleOption = (id, action, index, value) => {
    const field = customFields.find(f => f.id === id);
    if (!field) return;
    let newOpts = [...(field.options || [])];
    if (action === 'add') newOpts.push(`Opsi ${newOpts.length + 1}`);
    if (action === 'update') newOpts[index] = value;
    if (action === 'remove') newOpts = newOpts.filter((_, i) => i !== index);
    updateField(id, 'options', newOpts);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const fixedFields = [
      { id: 'teamName', label: 'Nama Tim', type: 'text', required: true, isFixed: true },
      { id: 'captainName', label: 'Nama Kapten', type: 'text', required: true, isFixed: true },
      { id: 'whatsapp', label: 'WhatsApp Kapten', type: 'tel', required: true, isFixed: true },
    ];
    onSubmit({ ...form, formSchema: [...fixedFields, ...customFields] });
  };

  // Helper to render type icon
  const getTypeIcon = (type) => {
    switch(type) {
      case 'text': return <Type size={14}/>;
      case 'textarea': return <AlignLeft size={14}/>;
      case 'radio': return <Circle size={14}/>;
      case 'checkbox': return <CheckSquare size={14}/>;
      case 'select': return <ArrowDownCircle size={14}/>;
      case 'date': return <Calendar size={14}/>;
      case 'time': return <Clock size={14}/>;
      case 'datetime-local': return <Clock size={14}/>;
      case 'file': return <UploadCloud size={14}/>;
      case 'note': return <MessageSquare size={14}/>;
      case 'image-view': return <ImageIcon size={14}/>;
      case 'link': return <LinkIcon size={14}/>;
      default: return <Type size={14}/>;
    }
  };

  return (
    <div className="flex flex-col md:flex-row gap-6 items-start relative">
      <form onSubmit={handleSubmit} className="flex-1 space-y-6 w-full">
        {/* Header Config */}
        <div className="bg-[#1a1a1a] p-6 rounded-xl border-t-8 border-cyan-500 shadow-xl space-y-4">
          <input required type="text" className="w-full bg-transparent border-b border-gray-700 text-3xl font-bold text-white placeholder-gray-600 outline-none pb-2 focus:border-cyan-500 transition" 
            value={form.title} onChange={e => setForm({...form, title: e.target.value})} placeholder="Judul Turnamen"/>
          <textarea className="w-full bg-transparent border-b border-gray-700 text-sm text-gray-300 placeholder-gray-600 outline-none resize-none h-20 focus:border-cyan-500 transition"
            value={form.description} onChange={e => setForm({...form, description: e.target.value})} placeholder="Deskripsi Formulir / Peraturan..."/>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-800">
             <div><label className="text-[10px] uppercase text-gray-500 font-bold">Game</label><select className="w-full bg-black border border-gray-700 rounded p-1 text-sm" value={form.game} onChange={e => setForm({...form, game: e.target.value})}><option>Mobile Legends</option><option>Valorant</option><option>PUBG Mobile</option><option>Free Fire</option></select></div>
             <div><label className="text-[10px] uppercase text-gray-500 font-bold">Waktu</label><input type="datetime-local" className="w-full bg-black border border-gray-700 rounded p-1 text-sm" value={form.date} onChange={e => setForm({...form, date: e.target.value})} required/></div>
             <div><label className="text-[10px] uppercase text-gray-500 font-bold">Biaya</label><input type="number" className="w-full bg-black border border-gray-700 rounded p-1 text-sm" value={form.fee} onChange={e => setForm({...form, fee: e.target.value})} required/></div>
             <div><label className="text-[10px] uppercase text-gray-500 font-bold">Slot</label><input type="number" className="w-full bg-black border border-gray-700 rounded p-1 text-sm" value={form.slot} onChange={e => setForm({...form, slot: e.target.value})} required/></div>
          </div>
        </div>

        {/* Dynamic Fields */}
        <div className="space-y-4 pb-20">
          {customFields.map((field) => (
            <div key={field.id} className="bg-[#1a1a1a] p-5 rounded-xl border border-gray-800 shadow-md group relative hover:border-l-4 hover:border-l-cyan-500 transition-all">
              
              {/* STATIC BLOCKS */}
              {['note', 'image-view', 'link'].includes(field.type) ? (
                <div className="space-y-3">
                   <div className="flex justify-between items-center text-cyan-500 text-xs font-bold uppercase tracking-widest mb-2">
                      <span className="flex items-center gap-2">
                        {field.type === 'note' && <><Type size={14}/> Teks / Judul</>}
                        {field.type === 'image-view' && <><ImageIcon size={14}/> Gambar Banner</>}
                        {field.type === 'link' && <><LinkIcon size={14}/> Link Eksternal</>}
                      </span>
                      <button type="button" onClick={() => removeField(field.id)} className="text-gray-600 hover:text-red-500"><Trash2 size={16}/></button>
                   </div>
                   <input type="text" className="w-full bg-transparent border-b border-gray-700 p-2 text-lg font-bold text-white outline-none focus:border-cyan-500"
                      value={field.label} onChange={e => updateField(field.id, 'label', e.target.value)} placeholder={field.type === 'image-view' ? "Judul Gambar (Opsional)" : "Judul / Label"}/>
                   
                   {field.type === 'note' && <textarea className="w-full bg-black/50 border border-gray-700 rounded p-2 text-sm text-white outline-none h-20" value={field.description} onChange={e => updateField(field.id, 'description', e.target.value)} placeholder="Isi teks..."/>}
                   
                   {field.type === 'image-view' && (
                     <div className="space-y-2">
                       <label className="flex items-center gap-2 px-3 py-2 bg-black border border-gray-700 rounded cursor-pointer hover:border-cyan-500 transition w-fit">
                          <UploadCloud size={16} className="text-cyan-500"/>
                          <span className="text-xs text-gray-300">Upload Gambar Banner</span>
                          <input type="file" accept="image/*" className="hidden" onChange={(e) => handleBuilderImageUpload(e, (base64) => updateField(field.id, 'description', base64))}/>
                       </label>
                       {field.description && (
                         <div className="relative w-fit">
                           <img src={field.description} className="h-24 rounded border border-gray-700 object-cover" alt="Preview"/>
                           <button type="button" onClick={() => updateField(field.id, 'description', '')} className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"><X size={12}/></button>
                         </div>
                       )}
                     </div>
                   )}

                   {field.type === 'link' && <input type="text" className="w-full bg-black/50 border border-gray-700 rounded p-2 text-sm text-cyan-400 outline-none" value={field.description} onChange={e => updateField(field.id, 'description', e.target.value)} placeholder="URL Link (https://...)"/>}
                </div>
              ) : (
                // QUESTION BLOCKS
                <div className="space-y-4">
                  <div className="flex flex-col md:flex-row gap-4">
                    <div className="flex-1">
                      <input type="text" className="w-full bg-[#222] border-b border-gray-700 p-3 text-base text-white outline-none focus:border-cyan-500 rounded-t"
                        value={field.label} onChange={e => updateField(field.id, 'label', e.target.value)} placeholder="Pertanyaan"/>
                    </div>
                    <div className="w-full md:w-40 relative">
                      <select className="w-full bg-[#222] border border-gray-700 rounded p-2 text-sm text-gray-300 focus:border-cyan-500 outline-none"
                        value={field.type} onChange={e => updateField(field.id, 'type', e.target.value)}>
                        <option value="text">Jawaban Singkat</option><option value="textarea">Paragraf</option>
                        <option value="radio">Pilihan Ganda</option><option value="checkbox">Kotak Centang</option>
                        <option value="select">Dropdown</option><option value="file">Upload File</option>
                        <option value="date">Tanggal</option><option value="time">Waktu</option>
                        <option value="datetime-local">Tanggal & Waktu</option>
                      </select>
                    </div>
                  </div>

                  {/* MEDIA EMBEDDER (INDEPENDENT TOGGLES) */}
                  <div className="flex flex-col gap-3 bg-[#111] p-3 rounded border border-gray-800">
                    <div className="flex items-center gap-3">
                        <span className="text-[10px] text-gray-500 font-bold uppercase mr-2">Sisipkan:</span>
                        {/* Image Toggle */}
                        <button type="button" onClick={() => updateField(field.id, 'showImageInput', !field.showImageInput)} 
                        className={`p-1 rounded transition ${field.imageUrl || field.showImageInput ? 'text-cyan-400 bg-cyan-900/30' : 'text-gray-500 hover:text-white'}`} title="Sisipkan Gambar">
                        <ImageIcon size={16}/>
                        </button>
                        {/* Link Toggle */}
                        <button type="button" onClick={() => updateField(field.id, 'showLinkInput', !field.showLinkInput)} 
                        className={`p-1 rounded transition ${field.linkUrl || field.showLinkInput ? 'text-cyan-400 bg-cyan-900/30' : 'text-gray-500 hover:text-white'}`} title="Sisipkan Link">
                        <LinkIcon size={16}/>
                        </button>
                    </div>
                    
                    {/* Media Inputs Area */}
                    <div className="space-y-3">
                        {/* Image Input */}
                        {(field.showImageInput || field.imageUrl) && (
                            <div className="flex flex-col gap-2 animate-fade-in bg-black/30 p-2 rounded border border-gray-800">
                                <div className="flex justify-between items-center">
                                    <label className="flex items-center gap-2 cursor-pointer bg-black border border-gray-700 rounded px-2 py-1 w-fit hover:border-cyan-500">
                                        <UploadCloud size={12} className="text-gray-400"/>
                                        <span className="text-[10px] text-gray-300">Pilih Gambar</span>
                                        <input type="file" accept="image/*" className="hidden" onChange={(e) => handleBuilderImageUpload(e, (base64) => updateField(field.id, 'imageUrl', base64))}/>
                                    </label>
                                    <button type="button" onClick={() => { updateField(field.id, 'imageUrl', ''); updateField(field.id, 'showImageInput', false); }} className="text-gray-600 hover:text-red-500"><X size={12}/></button>
                                </div>
                                {field.imageUrl && <img src={field.imageUrl} className="h-24 rounded border border-gray-600 object-cover w-fit" alt="Preview"/>}
                            </div>
                        )}
                        {/* Link Input */}
                        {(field.showLinkInput || field.linkUrl) && (
                            <div className="flex gap-2 animate-fade-in bg-black/30 p-2 rounded border border-gray-800 items-center">
                                <div className="flex-1 flex flex-col gap-2">
                                    <input type="text" className="bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500" 
                                    placeholder="URL Link (https://...)" value={field.linkUrl || ''} onChange={e => updateField(field.id, 'linkUrl', e.target.value)}/>
                                    <input type="text" className="bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500" 
                                    placeholder="Teks Link (Optional)" value={field.linkText || ''} onChange={e => updateField(field.id, 'linkText', e.target.value)}/>
                                </div>
                                <button type="button" onClick={() => { updateField(field.id, 'linkUrl', ''); updateField(field.id, 'linkText', ''); updateField(field.id, 'showLinkInput', false); }} className="text-gray-600 hover:text-red-500"><X size={12}/></button>
                            </div>
                        )}
                    </div>
                  </div>

                  <input type="text" className="w-full bg-transparent border-b border-dashed border-gray-800 text-xs text-gray-500 focus:text-white p-1 outline-none"
                    value={field.description || ''} onChange={e => updateField(field.id, 'description', e.target.value)} placeholder="Deskripsi / Keterangan Soal (Opsional)"/>

                  {/* FILE UPLOAD SETTINGS */}
                  {field.type === 'file' && (
                    <div className="ml-2 mt-4 space-y-4 border-l-2 border-cyan-500 pl-4 bg-cyan-900/10 p-4 rounded-r-lg">
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-gray-300">Izinkan hanya jenis file tertentu</span>
                            <div className="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" name="toggle" id={`toggle-${field.id}`} 
                                    className="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                    checked={field.fileSettings?.restrictTypes || false}
                                    onChange={(e) => updateFileSettings(field.id, 'restrictTypes', e.target.checked)}
                                />
                                <label htmlFor={`toggle-${field.id}`} className={`toggle-label block overflow-hidden h-5 rounded-full cursor-pointer ${field.fileSettings?.restrictTypes ? 'bg-cyan-600' : 'bg-gray-600'}`}></label>
                            </div>
                        </div>
                        {field.fileSettings?.restrictTypes && (
                            <div className="grid grid-cols-2 gap-3 animate-fade-in">
                                {[{id: 'document', label: 'Dokumen', icon: <FileText size={14}/>}, {id: 'spreadsheet', label: 'Spreadsheet', icon: <List size={14}/>}, {id: 'pdf', label: 'PDF', icon: <File size={14}/>}, {id: 'video', label: 'Video', icon: <Video size={14}/>}, {id: 'image', label: 'Gambar', icon: <ImageIcon size={14}/>}, {id: 'audio', label: 'Audio', icon: <Music size={14}/>}].map(type => (
                                    <label key={type.id} className="flex items-center gap-2 cursor-pointer text-sm text-gray-400 hover:text-white">
                                        <input type="checkbox" checked={(field.fileSettings?.allowedTypes || []).includes(type.id)} onChange={() => toggleFileType(field.id, type.id)} className="accent-cyan-500 w-4 h-4 rounded"/>{type.icon} {type.label}
                                    </label>
                                ))}
                            </div>
                        )}
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-gray-300">Ukuran file maksimum</span>
                            <select className="bg-black border border-gray-700 rounded p-1 text-sm text-white focus:border-cyan-500 outline-none" value={field.fileSettings?.maxSize || 1048576} onChange={(e) => updateFileSettings(field.id, 'maxSize', parseInt(e.target.value))}>
                                <option value={1048576}>1 MB</option><option value={524288}>500 KB</option><option value={204800}>200 KB</option>
                            </select>
                        </div>
                    </div>
                  )}

                  {/* Options for Choice Questions */}
                  {['radio', 'checkbox', 'select'].includes(field.type) && (
                    <div className="ml-2 space-y-2 pl-2 border-l-2 border-gray-800">
                      {field.options?.map((opt, i) => (
                        <div key={i} className="flex items-center gap-2">
                          <div className="text-gray-600">{field.type === 'radio' ? <Circle size={12}/> : field.type === 'checkbox' ? <CheckSquare size={12}/> : <span className="text-[10px]">{i+1}.</span>}</div>
                          <input type="text" className="bg-transparent border-b border-gray-800 hover:border-gray-600 focus:border-cyan-500 text-sm text-gray-300 w-full p-1 outline-none"
                            value={opt} onChange={e => handleOption(field.id, 'update', i, e.target.value)}/>
                          <button type="button" onClick={() => handleOption(field.id, 'remove', i)} className="text-gray-600 hover:text-red-500"><X size={14}/></button>
                        </div>
                      ))}
                      <button type="button" onClick={() => handleOption(field.id, 'add')} className="text-cyan-500 text-xs hover:underline flex items-center gap-1 mt-2 p-1"><Plus size={12}/> Tambah Opsi</button>
                    </div>
                  )}

                  <div className="flex justify-end items-center gap-4 pt-4 border-t border-gray-800 mt-4">
                    <div className="flex items-center gap-2 border-r border-gray-700 pr-4">
                      <span className="text-xs text-gray-500 cursor-pointer" onClick={() => updateField(field.id, 'required', !field.required)}>Wajib</span>
                      <div className={`w-8 h-4 rounded-full cursor-pointer relative transition ${field.required ? 'bg-cyan-600' : 'bg-gray-700'}`} onClick={() => updateField(field.id, 'required', !field.required)}>
                        <div className={`w-2 h-2 bg-white rounded-full absolute top-1 transition-all ${field.required ? 'left-5' : 'left-1'}`}></div>
                      </div>
                    </div>
                    <button type="button" onClick={() => duplicateField(field)} className="text-gray-500 hover:text-white p-2" title="Duplikasi"><Copy size={16}/></button>
                    <button type="button" onClick={() => removeField(field.id)} className="text-gray-500 hover:text-red-500 p-2" title="Hapus"><Trash2 size={16}/></button>
                  </div>
                </div>
              )}
            </div>
          ))}
        </div>

        <div className="flex items-center gap-4 pt-4 border-t border-gray-800">
            <button type="button" onClick={onCancel} className="px-6 py-3 border border-gray-700 rounded-lg text-gray-300 hover:text-white hover:border-gray-500">Batal</button>
            <button type="submit" className="flex-1 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold py-3 rounded-lg shadow-lg">
                {isEdit ? 'Simpan Perubahan' : 'Buat Turnamen'}
            </button>
        </div>
      </form>

      {/* FLOATING SIDEBAR */}
      <div className="sticky top-24 bg-[#1a1a1a] border border-gray-700 rounded-lg shadow-2xl p-2 flex flex-col gap-3 hidden md:flex animate-fade-in">
        <button onClick={() => addField('text', '')} className="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative">
            <Plus size={24} strokeWidth={2.5}/>
            <span className="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah Soal</span>
        </button>
        <button onClick={() => addField('note', 'Judul Baru')} className="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative">
            <Type size={20} />
            <span className="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah Teks</span>
        </button>
        <button onClick={() => addField('image-view', '')} className="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative">
            <ImageIcon size={20} />
            <span className="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah Gambar</span>
        </button>
        <button onClick={() => addField('link', 'Judul Link')} className="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative">
            <LinkIcon size={20} />
            <span className="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah Link</span>
        </button>
      </div>

      <div className="fixed bottom-0 left-0 w-full bg-[#1a1a1a] border-t border-gray-800 p-3 flex justify-around md:hidden z-50">
        <button onClick={() => addField('text', '')} className="text-gray-400 hover:text-cyan-400"><Plus size={24}/></button>
        <button onClick={() => addField('note', 'Judul')} className="text-gray-400 hover:text-cyan-400"><Type size={24}/></button>
        <button onClick={() => addField('image-view', '')} className="text-gray-400 hover:text-cyan-400"><ImageIcon size={24}/></button>
        <button onClick={() => addField('link', 'Link')} className="text-gray-400 hover:text-cyan-400"><LinkIcon size={24}/></button>
      </div>
    </div>
  );
};

const PublicRegistration = ({ tournament, onSubmit, onBack }) => {
  const [step, setStep] = useState(1);
  const [formData, setFormData] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const formRef = useRef(null);

  // CHECK IF CLOSED
  if (tournament.status === 'closed') {
    return (
        <div className="max-w-xl mx-auto py-20 px-4 text-center animate-fade-in">
            <div className="bg-[#1a1a1a] border-t-8 border-red-500 rounded-xl p-8 shadow-2xl">
                <Lock size={48} className="text-red-500 mx-auto mb-4"/>
                <h2 className="text-2xl font-bold text-white mb-2">Pendaftaran Ditutup</h2>
                <p className="text-gray-400 mb-6">Maaf, kuota sudah penuh atau waktu pendaftaran telah habis.</p>
                <button onClick={onBack} className="text-cyan-500 hover:underline">Kembali</button>
            </div>
        </div>
    );
  }

  const handleInputChange = (id, value) => setFormData(prev => ({ ...prev, [id]: value }));
  const handleCheckbox = (id, val, checked) => {
    setFormData(prev => {
        const cur = prev[id] || [];
        return { ...prev, [id]: checked ? [...cur, val] : cur.filter(v => v !== val) };
    });
  };
  
  // VALIDATE FILE TYPE & SIZE
  const validateFile = (file, settings) => {
    if (!file) return { valid: false, msg: 'File error' };
    const maxSize = settings?.maxSize || 1048576; // Default 1MB
    if (file.size > maxSize) return { valid: false, msg: `Ukuran file terlalu besar! Maksimal ${(maxSize/1024).toFixed(0)} KB.` };
    if (settings?.restrictTypes && settings?.allowedTypes?.length > 0) {
        const typeMap = { 'image': 'image/', 'pdf': 'application/pdf', 'video': 'video/', 'audio': 'audio/', 'document': ['msword', 'wordprocessing', 'plain'], 'spreadsheet': ['excel', 'spreadsheet'] };
        let isValidType = false;
        settings.allowedTypes.forEach(allowed => {
            const check = typeMap[allowed];
            if (Array.isArray(check)) { if (check.some(c => file.type.includes(c))) isValidType = true; } else if (file.type.includes(check)) { isValidType = true; }
        });
        if (!isValidType) return { valid: false, msg: `Tipe file tidak diizinkan.` };
    }
    return { valid: true };
  };

  const handleFile = (id, e, settings) => {
    const file = e.target.files[0];
    if (file) {
        const validation = validateFile(file, settings);
        if (!validation.valid) { alert(validation.msg); e.target.value = null; return; }
        const reader = new FileReader();
        reader.onloadend = () => handleInputChange(id, reader.result);
        reader.readAsDataURL(file);
    }
  };
  
  const handleClearForm = () => {
    if (window.confirm("Hapus semua isian?")) {
        setFormData({}); if (formRef.current) formRef.current.reset();
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault(); setIsSubmitting(true);
    if(await onSubmit(tournament.id, formData)) setStep(2); else setIsSubmitting(false);
  };

  if(step === 2) return (
    <div className="max-w-xl mx-auto py-20 px-4 text-center animate-fade-in">
        <div className="bg-[#1a1a1a] border-t-8 border-green-500 rounded-xl p-8 shadow-2xl">
            <CheckCircle size={48} className="text-green-500 mx-auto mb-4"/>
            <h2 className="text-2xl font-bold text-white mb-2">Terima Kasih!</h2>
            <p className="text-gray-400 mb-6">Pendaftaran Anda telah berhasil direkam.</p>
            <button onClick={onBack} className="text-cyan-500 hover:underline">Kembali</button>
        </div>
    </div>
  );

  return (
    <div className="max-w-xl mx-auto py-10 px-4 pb-32 animate-fade-in relative">
      <button onClick={onBack} className="absolute top-2 left-4 text-xs text-gray-600 hover:text-white">Exit Preview</button>
      
      <div className="bg-[#1a1a1a] border-t-8 border-cyan-500 rounded-xl p-6 mb-6 shadow-2xl">
        <h1 className="text-3xl font-display font-bold text-white mb-2">{tournament.title}</h1>
        <p className="text-gray-400 text-sm mb-4 whitespace-pre-wrap">{tournament.description || "Silakan isi data dengan benar."}</p>
        <div className="flex gap-4 text-xs text-gray-500 border-t border-gray-800 pt-4">
            <span className="flex items-center gap-1"><Gamepad2 size={12}/> {tournament.game}</span>
            <span className="flex items-center gap-1"><Calendar size={12}/> {formatDate(tournament.date)}</span>
            <span className="text-red-500 font-bold">* Wajib</span>
        </div>
      </div>

      <form ref={formRef} onSubmit={handleSubmit} className="space-y-4">
        {tournament.formSchema?.map((field) => {
            if(field.type === 'note') return <div key={field.id} className="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6"><h3 className="text-lg font-bold text-white">{field.label}</h3><p className="text-sm text-gray-400 whitespace-pre-wrap">{field.description}</p></div>;
            if(field.type === 'image-view') return <div key={field.id} className="bg-[#1a1a1a] border border-gray-800 rounded-xl p-2 text-center"><img src={field.description} className="w-full rounded-lg" alt="" onError={(e) => e.target.style.display='none'}/></div>;
            if(field.type === 'link') return <div key={field.id} onClick={()=>window.open(field.linkUrl || field.description, '_blank')} className="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 flex items-center gap-4 hover:border-cyan-500 cursor-pointer"><LinkIcon className="text-cyan-500"/><div className="font-bold text-cyan-400">{field.label}</div></div>;

            return (
                <div key={field.id} className={`bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 transition focus-within:border-cyan-500 ${field.required && !formData[field.id] && isSubmitting ? 'border-red-500' : ''}`}>
                    <label className="block text-sm font-medium text-white mb-1">{field.label} {field.required && <span className="text-red-500">*</span>}</label>
                    
                    {/* Media Embed Display (BOTH Image AND Link) */}
                    {field.imageUrl && (
                        <img src={field.imageUrl} className="w-full max-h-60 object-contain rounded border border-gray-700 mb-3 bg-black/30" alt=""/>
                    )}
                    {field.linkUrl && (
                        <a href={field.linkUrl} target="_blank" rel="noreferrer" className="inline-flex items-center gap-2 text-xs text-cyan-400 mb-3 hover:underline border border-cyan-900 px-3 py-2 rounded bg-cyan-950/20 w-full"><LinkIcon size={14}/> {field.linkText || field.linkUrl}</a>
                    )}
                    
                    {field.description && <p className="text-xs text-gray-500 mb-3 whitespace-pre-wrap">{field.description}</p>}

                    {field.type === 'textarea' ? <textarea required={field.required} className="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm h-24 outline-none focus:border-cyan-500" onChange={e => handleInputChange(field.id, e.target.value)}/>
                    : field.type === 'file' ? <input type="file" required={field.required} accept={field.fileSettings?.restrictTypes ? (field.fileSettings.allowedTypes.map(t => t==='image'?'image/*':t==='pdf'?'application/pdf':t==='video'?'video/*':t==='audio'?'audio/*':t==='spreadsheet'?'.xls,.xlsx':t==='document'?'.doc,.docx':'*').join(',')) : '*'} className="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-cyan-900 file:text-cyan-400 hover:file:bg-cyan-800" onChange={e => handleFile(field.id, e, field.fileSettings)}/>
                    : field.type === 'radio' ? <div className="space-y-2">{field.options?.map((opt, i) => <label key={i} className="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white"><input type="radio" name={field.id} value={opt} required={field.required} onChange={e => handleInputChange(field.id, e.target.value)} className="accent-cyan-500 w-4 h-4"/>{opt}</label>)}</div>
                    : field.type === 'checkbox' ? <div className="space-y-2">{field.options?.map((opt, i) => <label key={i} className="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white"><input type="checkbox" value={opt} onChange={e => handleCheckbox(field.id, opt, e.target.checked)} className="accent-cyan-500 w-4 h-4"/>{opt}</label>)}</div>
                    : field.type === 'select' ? <div className="relative"><select required={field.required} className="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm appearance-none outline-none focus:border-cyan-500" onChange={e => handleInputChange(field.id, e.target.value)} defaultValue=""><option value="" disabled>Pilih...</option>{field.options?.map((opt, i) => <option key={i} value={opt}>{opt}</option>)}</select><ArrowDownCircle size={16} className="absolute right-3 top-3 text-gray-500 pointer-events-none"/></div>
                    : <input type={field.type} required={field.required} className="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500" onChange={e => handleInputChange(field.id, e.target.value)}/>}
                </div>
            );
        })}

        <div className="flex justify-between items-center mt-8">
            <button type="submit" disabled={isSubmitting} className="bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-3 px-8 rounded-lg shadow-lg flex items-center gap-2">{isSubmitting ? <Loader /> : 'Kirim Pendaftaran'}</button>
            <button type="button" className="text-gray-500 text-xs hover:text-white flex items-center gap-2" onClick={handleClearForm}><RefreshCw size={14}/> Reset</button>
        </div>
      </form>
      <div className="text-center mt-12 text-[10px] text-gray-700">Powered by <strong>MIRAI Tournament Platform</strong></div>
    </div>
  );
};