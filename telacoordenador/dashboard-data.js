/**
 * Dashboard Data and Functionality
 * This file centralizes all data and functionality for the teacher dashboard
 */

// Initialize the dashboard data object
window.dashboardData = {
  // Current user data
  currentUser: {
    name: "Prof. Silva",
    subject: "Matemática",
    avatar: "https://via.placeholder.com/50",
    email: "carlos.silva@pitruca.com",
    phone: "(11) 98765-4321",
  },

  // Dynamic dates
  dates: {
    current: new Date(),
    schoolYear: "2025",
    currentPeriod: "1º Trimestre", // Changed from Bimestre to Trimestre
    lastUpdate: new Date(new Date().setDate(new Date().getDate() - 2)),
  },

  // Classes data
  classes: [
    {
      id: "9A",
      name: "9º Ano A",
      students: 28,
      schedule: "Segunda e Quarta, 07:30 - 09:10",
      room: "Sala 105",
      progress: 68,
    },
    {
      id: "8B",
      name: "8º Ano B",
      students: 32,
      schedule: "Terça e Quinta, 09:30 - 11:10",
      room: "Sala 203",
      progress: 75,
    },
    {
      id: "10C",
      name: "10º Ano C",
      students: 25,
      schedule: "Segunda e Sexta, 13:30 - 15:10",
      room: "Sala 108",
      progress: 82,
    },
    {
      id: "7D",
      name: "7º Ano D",
      students: 30,
      schedule: "Quarta e Sexta, 15:30 - 17:10",
      room: "Sala 201",
      progress: 60,
    },
  ],

  // Students data
  students: [
    {
      id: "ST001",
      name: "Ana Silva",
      avatar: "https://via.placeholder.com/30",
      class: "9A",
      attendance: 95,
      grades: {
        math: { av1: 8.5, av2: 9.0, av3: 8.8, average: 8.8 },
        portuguese: { av1: 7.5, av2: 8.0, av3: 7.8, average: 7.8 },
        science: { av1: 9.0, av2: 8.5, av3: 9.2, average: 8.9 },
        history: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
        geography: { av1: 7.5, av2: 8.0, av3: 7.8, average: 7.8 },
        english: { av1: 6.5, av2: 7.0, av3: 6.8, average: 6.8 },
        pe: { av1: 9.0, av2: 9.5, av3: 9.2, average: 9.2 },
      },
      comments:
        "A aluna Ana Silva demonstra excelente desempenho em Matemática e Ciências. Recomenda-se atenção especial à disciplina de Inglês, onde apresenta dificuldades. No geral, é uma aluna dedicada e participativa.",
    },
    {
      id: "ST002",
      name: "Pedro Santos",
      avatar: "https://via.placeholder.com/30",
      class: "9A",
      attendance: 88,
      grades: {
        math: { av1: 7.0, av2: 6.5, av3: 7.5, average: 7.0 },
        portuguese: { av1: 8.0, av2: 7.5, av3: 8.2, average: 7.9 },
        science: { av1: 6.5, av2: 7.0, av3: 6.8, average: 6.8 },
        history: { av1: 7.0, av2: 7.5, av3: 7.2, average: 7.2 },
        geography: { av1: 6.5, av2: 7.0, av3: 6.8, average: 6.8 },
        english: { av1: 7.5, av2: 8.0, av3: 7.8, average: 7.8 },
        pe: { av1: 8.5, av2: 8.0, av3: 8.8, average: 8.4 },
      },
      comments:
        "O aluno Pedro Santos tem demonstrado melhora em seu desempenho em Matemática. Continua com bom rendimento em Português. Recomenda-se atenção às atividades de Ciências.",
    },
    {
      id: "ST003",
      name: "Mariana Oliveira",
      avatar: "https://via.placeholder.com/30",
      class: "9A",
      attendance: 92,
      grades: {
        math: { av1: 9.5, av2: 9.0, av3: 9.8, average: 9.4 },
        portuguese: { av1: 8.5, av2: 9.0, av3: 8.8, average: 8.8 },
        science: { av1: 9.0, av2: 9.5, av3: 9.2, average: 9.2 },
        history: { av1: 9.0, av2: 8.5, av3: 9.2, average: 8.9 },
        geography: { av1: 8.5, av2: 9.0, av3: 8.8, average: 8.8 },
        english: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
        pe: { av1: 9.5, av2: 9.0, av3: 9.8, average: 9.4 },
      },
      comments:
        "A aluna Mariana Oliveira continua com desempenho excelente em todas as disciplinas. Demonstra grande interesse e participação nas aulas.",
    },
    {
      id: "ST004",
      name: "Lucas Pereira",
      avatar: "https://via.placeholder.com/30",
      class: "8B",
      attendance: 78,
      grades: {
        math: { av1: 6.0, av2: 5.5, av3: 6.5, average: 6.0 },
        portuguese: { av1: 7.0, av2: 6.5, av3: 7.2, average: 6.9 },
        science: { av1: 6.5, av2: 6.0, av3: 6.8, average: 6.4 },
        history: { av1: 6.0, av2: 6.5, av3: 6.2, average: 6.2 },
        geography: { av1: 5.5, av2: 6.0, av3: 5.8, average: 5.8 },
        english: { av1: 5.0, av2: 5.5, av3: 5.2, average: 5.2 },
        pe: { av1: 7.5, av2: 7.0, av3: 7.8, average: 7.4 },
      },
      comments:
        "O aluno Lucas Pereira precisa melhorar sua frequência às aulas. Seu desempenho tem sido prejudicado pelas faltas. Recomenda-se acompanhamento mais próximo.",
    },
    {
      id: "ST005",
      name: "Juliana Costa",
      avatar: "https://via.placeholder.com/30",
      class: "8B",
      attendance: 96,
      grades: {
        math: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
        portuguese: { av1: 9.0, av2: 9.5, av3: 9.2, average: 9.2 },
        science: { av1: 8.5, av2: 8.0, av3: 8.8, average: 8.4 },
        history: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
        geography: { av1: 8.5, av2: 8.0, av3: 8.8, average: 8.4 },
        english: { av1: 9.0, av2: 8.5, av3: 9.2, average: 8.9 },
        pe: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
      },
      comments:
        "A aluna Juliana Costa demonstra excelente comprometimento com os estudos. Tem se destacado especialmente em Português.",
    },
    {
      id: "ST006",
      name: "Rafael Souza",
      avatar: "https://via.placeholder.com/30",
      class: "10C",
      attendance: 85,
      grades: {
        math: { av1: 7.5, av2: 8.0, av3: 7.8, average: 7.8 },
        portuguese: { av1: 6.5, av2: 7.0, av3: 6.8, average: 6.8 },
        science: { av1: 7.0, av2: 7.5, av3: 7.2, average: 7.2 },
        history: { av1: 7.5, av2: 7.0, av3: 7.8, average: 7.4 },
        geography: { av1: 7.0, av2: 7.5, av3: 7.2, average: 7.2 },
        english: { av1: 6.0, av2: 6.5, av3: 6.2, average: 6.2 },
        pe: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
      },
      comments:
        "O aluno Rafael Souza tem demonstrado bom desempenho em Matemática. Precisa dedicar mais atenção à disciplina de Português.",
    },
    {
      id: "ST007",
      name: "Camila Almeida",
      avatar: "https://via.placeholder.com/30",
      class: "10C",
      attendance: 90,
      grades: {
        math: { av1: 8.0, av2: 7.5, av3: 8.2, average: 7.9 },
        portuguese: { av1: 8.5, av2: 8.0, av3: 8.8, average: 8.4 },
        science: { av1: 9.0, av2: 8.5, av3: 9.2, average: 8.9 },
        history: { av1: 8.5, av2: 8.0, av3: 8.8, average: 8.4 },
        geography: { av1: 8.0, av2: 8.5, av3: 8.2, average: 8.2 },
        english: { av1: 7.5, av2: 8.0, av3: 7.8, average: 7.8 },
        pe: { av1: 9.0, av2: 8.5, av3: 9.2, average: 8.9 },
      },
      comments:
        "A aluna Camila Almeida tem mantido bom desempenho em todas as disciplinas. Demonstra grande interesse por Ciências.",
    },
    {
      id: "ST008",
      name: "Bruno Lima",
      avatar: "https://via.placeholder.com/30",
      class: "7D",
      attendance: 82,
      grades: {
        math: { av1: 6.5, av2: 7.0, av3: 6.8, average: 6.8 },
        portuguese: { av1: 7.0, av2: 7.5, av3: 7.2, average: 7.2 },
        science: { av1: 7.5, av2: 7.0, av3: 7.8, average: 7.4 },
        history: { av1: 7.0, av2: 6.5, av3: 7.2, average: 6.9 },
        geography: { av1: 6.5, av2: 7.0, av3: 6.8, average: 6.8 },
        english: { av1: 6.0, av2: 6.5, av3: 6.2, average: 6.2 },
        pe: { av1: 8.0, av2: 7.5, av3: 8.2, average: 7.9 },
      },
      comments:
        "O aluno Bruno Lima tem demonstrado melhora em seu desempenho. Recomenda-se continuar com o acompanhamento para manter o progresso.",
    },
  ],

  // Calendar events
  events: [
    {
      id: "EV001",
      title: "Prova de Matemática - 9º Ano A",
      date: new Date(2025, 3, 18, 8, 0),
      endDate: new Date(2025, 3, 18, 10, 0),
      type: "assessment-event",
      location: "Sala 105",
      description: "Avaliação sobre Álgebra e Geometria",
    },
    {
      id: "EV002",
      title: "Conselho de Classe",
      date: new Date(2025, 3, 20, 13, 30),
      endDate: new Date(2025, 3, 20, 17, 0),
      type: "meeting-event",
      location: "Sala de Reuniões",
      description: "Conselho de classe do 1º trimestre",
    },
    {
      id: "EV003",
      title: "Entrega de Trabalhos - 8º Ano B",
      date: new Date(2025, 3, 25),
      type: "deadline-event",
      description: "Trabalho sobre Geometria Espacial",
    },
    {
      id: "EV004",
      title: "Aula de Matemática - 9º Ano A",
      date: new Date(2025, 3, 17, 7, 30),
      endDate: new Date(2025, 3, 17, 9, 10),
      type: "class-event",
      location: "Sala 105",
      description: "Conteúdo: Equações do 2º grau",
    },
    {
      id: "EV005",
      title: "Feriado - Tiradentes",
      date: new Date(2025, 3, 21),
      type: "holiday",
      description: "Feriado nacional",
    },
  ],

  // Materials data
  materials: [
    {
      id: "MAT001",
      title: "Apostila de Álgebra",
      type: "pdf",
      size: "2.5 MB",
      lastUpdate: new Date(2025, 4, 10),
      tags: ["9º Ano A", "Álgebra"],
      url: "#",
    },
    {
      id: "MAT002",
      title: "Apresentação sobre Geometria",
      type: "ppt",
      size: "5.8 MB",
      lastUpdate: new Date(2025, 4, 5),
      tags: ["8º Ano B", "Geometria"],
      url: "#",
    },
    {
      id: "MAT003",
      title: "Vídeo Explicativo: Estatística Básica",
      type: "video",
      size: "45 MB",
      lastUpdate: new Date(2025, 4, 12),
      tags: ["10º Ano C", "Estatística"],
      url: "#",
    },
    {
      id: "MAT004",
      title: "Lista de Exercícios: Equações",
      type: "doc",
      size: "1.2 MB",
      lastUpdate: new Date(2025, 4, 8),
      tags: ["9º Ano A", "Álgebra"],
      url: "#",
    },
    {
      id: "MAT005",
      title: "Planilha de Dados Estatísticos",
      type: "xls",
      size: "0.8 MB",
      lastUpdate: new Date(2025, 4, 15),
      tags: ["10º Ano C", "Estatística"],
      url: "#",
    },
    {
      id: "MAT006",
      title: "Recursos para Aula de Aritmética",
      type: "zip",
      size: "15.3 MB",
      lastUpdate: new Date(2025, 4, 3),
      tags: ["7º Ano D", "Aritmética"],
      url: "#",
    },
  ],

  // Messages data
  messages: [
    {
      id: "MSG001",
      sender: "Coordenação Pedagógica",
      avatar: "https://via.placeholder.com/30",
      date: new Date(2025, 4, 15, 10, 30),
      subject: "Reunião de Planejamento",
      content:
        "Prezado(a) Professor(a), informamos que a reunião de planejamento será realizada na próxima sexta-feira, às 14h, na sala de reuniões. Sua presença é indispensável.",
      read: true,
    },
    {
      id: "MSG002",
      sender: "Maria Oliveira (Mãe da Mariana)",
      avatar: "https://via.placeholder.com/30",
      date: new Date(2025, 4, 14, 18, 45),
      subject: "Dúvida sobre o trabalho",
      content:
        "Olá Professor, gostaria de saber mais detalhes sobre o trabalho de Matemática que foi passado para a Mariana. Ela está com algumas dúvidas sobre como proceder.",
      read: false,
    },
    {
      id: "MSG003",
      sender: "Direção",
      avatar: "https://via.placeholder.com/30",
      date: new Date(2025, 4, 12, 9, 15),
      subject: "Comunicado Importante",
      content:
        "Informamos que na próxima semana teremos a visita dos avaliadores do MEC. Pedimos que todos os professores mantenham seus planos de aula e registros atualizados.",
      read: true,
    },
    {
      id: "MSG004",
      sender: "Sistema",
      avatar: "https://via.placeholder.com/30",
      date: new Date(2025, 4, 10, 8, 0),
      subject: "Notas Lançadas com Sucesso",
      content:
        "As notas do 1º trimestre foram lançadas com sucesso no sistema. Os boletins já estão disponíveis para impressão.",
      read: true,
    },
  ],

  // Attendance data
  attendance: {
    dates: [
      new Date(2025, 3, 3),
      new Date(2025, 3, 5),
      new Date(2025, 3, 10),
      new Date(2025, 3, 12),
      new Date(2025, 3, 17),
      new Date(2025, 3, 19),
      new Date(2025, 3, 24),
      new Date(2025, 3, 26),
    ],
    records: {
      ST001: [true, true, true, true, true, true, true, false],
      ST002: [true, true, false, true, true, true, true, true],
      ST003: [true, true, true, true, true, false, true, true],
      ST004: [false, true, true, false, true, true, false, true],
      ST005: [true, true, true, true, true, true, true, true],
      ST006: [true, false, true, true, true, true, false, true],
      ST007: [true, true, true, false, true, true, true, true],
      ST008: [true, false, true, true, false, true, true, true],
    },
  },

  // Methods to update dynamic content
  updateDynamicDates: function () {
    const dynamicDateElements = document.querySelectorAll('[data-dynamic^="current-"]')

    dynamicDateElements.forEach((element) => {
      const dateType = element.getAttribute("data-dynamic")
      const format = element.getAttribute("data-format") || "short"

      if (dateType === "current-date") {
        element.textContent = this.formatDate(this.dates.current, format)
      } else if (dateType === "current-month") {
        element.textContent = this.formatMonth(this.dates.current)
      } else if (dateType === "current-period") {
        element.textContent = this.dates.currentPeriod
      } else if (dateType === "last-update") {
        element.textContent = this.formatDate(this.dates.lastUpdate, format)
      }
    })

    // Update school year
    const schoolYearElements = document.querySelectorAll('[data-dynamic="school-year"]')
    schoolYearElements.forEach((element) => {
      element.textContent = this.dates.schoolYear
    })
  },

  loadCurrentUserData: function () {
    // Update user name
    const userNameElements = document.querySelectorAll('[data-dynamic="user-name"]')
    userNameElements.forEach((element) => {
      element.textContent = this.currentUser.name
    })

    // Update user subject
    const userSubjectElements = document.querySelectorAll('[data-dynamic="user-subject"]')
    userSubjectElements.forEach((element) => {
      element.textContent = this.currentUser.subject
    })

    // Update user avatar
    const userAvatarElements = document.querySelectorAll('[data-dynamic="user-avatar"]')
    userAvatarElements.forEach((element) => {
      element.src = this.currentUser.avatar
    })
  },

  loadClassesData: function (periodFilter = "todos") {
    const classesContainer = document.querySelector('[data-dynamic="classes-container"]')
    if (!classesContainer) return

    // Clear the container
    classesContainer.innerHTML = ""

    // Filter classes if needed
    const filteredClasses = this.classes

    // Add each class card
    filteredClasses.forEach((classData) => {
      const classCard = document.createElement("div")
      classCard.className = "class-card"
      classCard.innerHTML = `
                <div class="class-card-header">
                    <h3>${classData.name}</h3>
                    <span class="badge">${classData.students} alunos</span>
                </div>
                <div class="class-card-content">
                    <p><span class="material-symbols-outlined">schedule</span> ${classData.schedule}</p>
                    <p><span class="material-symbols-outlined">room</span> ${classData.room}</p>
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Progresso do Conteúdo</span>
                            <span>${classData.progress}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" style="width: ${classData.progress}%"></div>
                        </div>
                    </div>
                </div>
                <div class="class-card-actions">
                    <button class="btn-outline class-details-btn" data-class-id="${classData.id}">
                        <span class="material-symbols-outlined">visibility</span>
                        Ver Detalhes
                    </button>
                    <button class="btn-outline class-students-btn" data-class-id="${classData.id}">
                        <span class="material-symbols-outlined">group</span>
                        Alunos
                    </button>
                </div>
            `
      classesContainer.appendChild(classCard)
    })

    // Add event listeners to class buttons
    this.initializeClassButtons()
  },

  loadStudentsData: function () {
    const studentsContainer = document.querySelector('[data-dynamic="students-container"]')
    if (!studentsContainer) return

    // Clear the container
    studentsContainer.innerHTML = ""

    // Add each student
    this.students.forEach((student) => {
      const studentRow = document.createElement("tr")
      studentRow.innerHTML = `
                <td>
                    <div class="student-name">
                        <img src="${student.avatar}" alt="${student.name}">
                        <div>
                            <p>${student.name}</p>
                            <span class="text-muted">${this.classes.find((c) => c.id === student.class)?.name || ""}</span>
                        </div>
                    </div>
                </td>
                <td>${student.grades.math.average.toFixed(1)}</td>
                <td>${student.attendance}%</td>
                <td>
                    <span class="status-badge ${student.attendance >= 75 ? "active" : "inactive"}">
                        ${student.attendance >= 75 ? "Ativo" : "Inativo"}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <button class="action-btn view-student-btn" title="Ver Detalhes" data-student-id="${student.id}">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                        <button class="action-btn edit-student-btn" title="Editar" data-student-id="${student.id}">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button class="action-btn student-options-btn" title="Mais Opções" data-student-id="${student.id}">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </div>
                </td>
            `
      studentsContainer.appendChild(studentRow)
    })

    // Add event listeners to student buttons
    this.initializeStudentButtons()
  },

  loadCalendarData: function () {
    const calendarGrid = document.querySelector('[data-dynamic="calendar-grid"]')
    if (!calendarGrid) return

    // Clear the grid
    calendarGrid.innerHTML = ""

    // Get current month and year
    const currentDate = new Date()
    const currentMonth = currentDate.getMonth()
    const currentYear = currentDate.getFullYear()

    // Get first day of the month and total days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay()
    const totalDays = new Date(currentYear, currentMonth + 1, 0).getDate()

    // Previous month days
    const prevMonthDays = new Date(currentYear, currentMonth, 0).getDate()

    // Create calendar days
    let dayCount = 1
    let nextMonthDay = 1

    // Create 6 rows (weeks)
    for (let i = 0; i < 6; i++) {
      // Create 7 columns (days)
      for (let j = 0; j < 7; j++) {
        const dayCell = document.createElement("div")
        dayCell.className = "calendar-day"

        // Previous month days
        if (i === 0 && j < firstDay) {
          const prevDay = prevMonthDays - (firstDay - j - 1)
          dayCell.innerHTML = `<span class="day-number">${prevDay}</span>`
          dayCell.classList.add("other-month")
        }
        // Current month days
        else if (dayCount <= totalDays) {
          dayCell.innerHTML = `<span class="day-number">${dayCount}</span>`

          // Weekend
          if (j === 0 || j === 6) {
            dayCell.classList.add("weekend")
          }

          // Current day
          if (
            dayCount === currentDate.getDate() &&
            currentMonth === currentDate.getMonth() &&
            currentYear === currentDate.getFullYear()
          ) {
            dayCell.classList.add("current-day")
          }

          // Add events for this day
          this.events.forEach((event) => {
            const eventDate = new Date(event.date)
            if (
              eventDate.getDate() === dayCount &&
              eventDate.getMonth() === currentMonth &&
              eventDate.getFullYear() === currentYear
            ) {
              const eventElement = document.createElement("div")
              eventElement.className = `calendar-event ${event.type}`
              eventElement.innerHTML = `<span class="event-dot"></span>${event.title}`
              eventElement.setAttribute("data-event-id", event.id)
              dayCell.appendChild(eventElement)
            }
          })

          dayCount++
        }
        // Next month days
        else {
          dayCell.innerHTML = `<span class="day-number">${nextMonthDay}</span>`
          dayCell.classList.add("other-month")
          nextMonthDay++
        }

        calendarGrid.appendChild(dayCell)
      }
    }

    // Add event listeners to calendar events
    this.initializeCalendarEvents()
  },

  updateGradesTable: (student) => {
    const gradesTableBody = document.getElementById("grades-table-body")
    if (!gradesTableBody) return

    // Clear the table
    gradesTableBody.innerHTML = ""

    // Add rows for each subject
    const subjects = {
      math: "Matemática",
      portuguese: "Português",
      science: "Ciências",
      history: "História",
      geography: "Geografia",
      pe: "Educação Física",
      english: "Inglês",
    }

    Object.entries(subjects).forEach(([key, name]) => {
      if (student.grades[key]) {
        const row = document.createElement("tr")

        // Determine situation based on average
        let situation = "Aprovado"
        let situationClass = "approved"

        if (student.grades[key].average < 6.0) {
          situation = "Reprovado"
          situationClass = "failed"
        } else if (student.grades[key].average < 7.0) {
          situation = "Recuperação"
          situationClass = "recovery"
        }

        row.innerHTML = `
                    <td>${name}</td>
                    <td>${student.grades[key].av1.toFixed(1)}</td>
                    <td>${student.grades[key].av2.toFixed(1)}</td>
                    <td>${student.grades[key].av3.toFixed(1)}</td>
                    <td>${student.grades[key].average.toFixed(1)}</td>
                    <td>${student.attendance}%</td>
                    <td class="${situationClass}">${situation}</td>
                `

        gradesTableBody.appendChild(row)
      }
    })
  },

  updatePerformanceChart: (student) => {
    const chartContainer = document.querySelector('[data-dynamic="performance-chart"]')
    if (!chartContainer) return

    // Clear the container
    chartContainer.innerHTML = ""

    // Create a simple bar chart
    const subjects = {
      math: "Matemática",
      portuguese: "Português",
      science: "Ciências",
      history: "História",
      geography: "Geografia",
      pe: "Educação Física",
      english: "Inglês",
    }

    const chartHtml = `
            <div class="chart">
                ${Object.entries(subjects)
                  .map(([key, name]) => {
                    if (student.grades[key]) {
                      const average = student.grades[key].average
                      const percentage = (average / 10) * 100
                      return `
                            <div class="chart-item">
                                <div class="chart-label">${name}</div>
                                <div class="chart-bar-container">
                                    <div class="chart-bar" style="width: ${percentage}%"></div>
                                    <div class="chart-value">${average.toFixed(1)}</div>
                                </div>
                            </div>
                        `
                    }
                    return ""
                  })
                  .join("")}
            </div>
        `

    chartContainer.innerHTML = chartHtml
  },

  // Helper methods
  formatDate: (date, format = "short") => {
    if (!date) return ""

    const day = date.getDate()
    const month = date.getMonth() + 1
    const year = date.getFullYear()

    if (format === "short") {
      return `${day.toString().padStart(2, "0")}/${month.toString().padStart(2, "0")}/${year}`
    } else if (format === "medium") {
      const monthNames = [
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro",
      ]
      return `${day} de ${monthNames[month - 1]} de ${year}`
    } else if (format === "full") {
      const monthNames = [
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro",
      ]
      const dayNames = [
        "Domingo",
        "Segunda-feira",
        "Terça-feira",
        "Quarta-feira",
        "Quinta-feira",
        "Sexta-feira",
        "Sábado",
      ]
      return `${dayNames[date.getDay()]}, ${day} de ${monthNames[month - 1]} de ${year}`
    }

    return `${day.toString().padStart(2, "0")}/${month.toString().padStart(2, "0")}/${year}`
  },

  formatMonth: (date) => {
    if (!date) return ""

    const monthNames = [
      "Janeiro",
      "Fevereiro",
      "Março",
      "Abril",
      "Maio",
      "Junho",
      "Julho",
      "Agosto",
      "Setembro",
      "Outubro",
      "Novembro",
      "Dezembro",
    ]
    return `${monthNames[date.getMonth()]} ${date.getFullYear()}`
  },

  // Initialize all buttons and interactive elements
  initializeAll: function () {
    // Initialize sidebar toggle
    this.initializeSidebar()

    // Initialize search functionality
    this.initializeSearch()

    // Initialize page-specific functionality
    this.initializeDashboard()
    this.initializeStudentsPage()
    this.initializeClassesPage()
    this.initializeGradesPage()
    this.initializeAttendancePage()
    this.initializeCalendarPage()
    this.initializeMaterialsPage()
    this.initializeMessagesPage()
    this.initializeReportsPage()
    this.initializeSettingsPage()
    this.initializeMiniPautaPage()

    // Update dynamic dates
    this.updateDynamicDates()

    // Load user data
    this.loadCurrentUserData()

    // Initialize all buttons from the provided script
    this.initializeButtonsAndInteractions()
  },

  // Initialize sidebar functionality
  initializeSidebar: () => {
    const menuToggle = document.getElementById("menuToggle")
    if (menuToggle) {
      menuToggle.addEventListener("click", () => {
        document.querySelector(".sidebar").classList.toggle("collapsed")
        document.querySelector(".content").classList.toggle("expanded")
      })
    }
  },

  // Initialize search functionality
  initializeSearch: () => {
    const searchInput = document.querySelector(".search-container input")
    if (searchInput) {
      searchInput.addEventListener("input", function () {
        // Implement search functionality based on the current page
        const currentPage = window.location.pathname.split("/").pop()

        if (currentPage === "alunos.html") {
          // Search students
          const searchTerm = this.value.toLowerCase()
          const studentRows = document.querySelectorAll('[data-dynamic="students-container"] tr')

          studentRows.forEach((row) => {
            const studentName = row.querySelector(".student-name p").textContent.toLowerCase()
            const studentClass = row.querySelector(".student-name .text-muted").textContent.toLowerCase()

            if (studentName.includes(searchTerm) || studentClass.includes(searchTerm)) {
              row.style.display = ""
            } else {
              row.style.display = "none"
            }
          })
        } else if (currentPage === "turmas.html") {
          // Search classes
          const searchTerm = this.value.toLowerCase()
          const classCards = document.querySelectorAll(".class-card")

          classCards.forEach((card) => {
            const className = card.querySelector("h3").textContent.toLowerCase()

            if (className.includes(searchTerm)) {
              card.style.display = ""
            } else {
              card.style.display = "none"
            }
          })
        } else if (currentPage === "materiais.html") {
          // Search materials
          const searchTerm = this.value.toLowerCase()
          const materialCards = document.querySelectorAll(".material-card")

          materialCards.forEach((card) => {
            if (card.classList.contains("upload-card")) return

            const materialTitle = card.querySelector("h3").textContent.toLowerCase()
            const materialTags = Array.from(card.querySelectorAll(".material-tag")).map((tag) =>
              tag.textContent.toLowerCase(),
            )

            if (materialTitle.includes(searchTerm) || materialTags.some((tag) => tag.includes(searchTerm))) {
              card.style.display = ""
            } else {
              card.style.display = "none"
            }
          })
        }
      })

      // Add focus and blur events
      searchInput.addEventListener("focus", function () {
        this.parentElement.classList.add("focused")
      })

      searchInput.addEventListener("blur", function () {
        this.parentElement.classList.remove("focused")
      })

      // Add Enter key event
      searchInput.addEventListener("keyup", function (e) {
        if (e.key === "Enter") {
          alert(`Pesquisando por: ${this.value}`)
          // Optionally clear the input
          // this.value = '';
        }
      })
    }
  },

  // Initialize dashboard page
  initializeDashboard: function () {
    // Check if we're on the dashboard page
    if (window.location.pathname.includes("index.html") || window.location.pathname.endsWith("/")) {
      // Update recent activities
      this.updateRecentActivities()

      // Initialize quick action buttons
      const quickActionButtons = document.querySelectorAll(".quick-action")
      quickActionButtons.forEach((button) => {
        button.addEventListener("click", function () {
          const action = this.getAttribute("data-action")

          if (action === "add-student") {
            alert("Funcionalidade: Adicionar Aluno")
            // Redirect to add student page or open modal
            // window.location.href = 'alunos.html?action=add';
          } else if (action === "take-attendance") {
            alert("Funcionalidade: Fazer Chamada")
            // Redirect to attendance page
            window.location.href = "presenca.html"
          } else if (action === "add-grades") {
            alert("Funcionalidade: Lançar Notas")
            // Redirect to grades page
            window.location.href = "notas.html"
          } else if (action === "print-reports") {
            alert("Funcionalidade: Imprimir Boletins")
            // Redirect to reports page
            window.location.href = "boletins.html"
          }
        })
      })

      // Initialize upcoming events
      const viewAllEventsBtn = document.querySelector(".upcoming-events .view-all")
      if (viewAllEventsBtn) {
        viewAllEventsBtn.addEventListener("click", () => {
          // Redirect to calendar page
          window.location.href = "calendario.html"
        })
      }

      // Initialize class performance chart
      this.initializePerformanceChart()
    }
  },

  // Initialize students page
  initializeStudentsPage: function () {
    // Check if we're on the students page
    if (window.location.pathname.includes("alunos.html")) {
      // Load students data
      this.loadStudentsData()

      // Initialize add student button
      const addStudentBtn = document.querySelector(".btn-primary")
      if (addStudentBtn) {
        addStudentBtn.addEventListener("click", () => {
          alert("Funcionalidade: Adicionar Aluno")
          // Open add student modal or redirect to add student page
        })
      }

      // Initialize filter functionality
      const turmaFilter = document.getElementById("turma-filter")
      const statusFilter = document.getElementById("status-filter")

      if (turmaFilter && statusFilter) {
        turmaFilter.addEventListener("change", function () {
          const turmaId = this.value
          const statusFilter = document.getElementById("status-filter").value
          window.dashboardData.filterStudents(turmaId, statusFilter)
        })

        statusFilter.addEventListener("change", function () {
          const statusFilter = this.value
          const turmaId = document.getElementById("turma-filter").value
          window.dashboardData.filterStudents(turmaId, statusFilter)
        })
      }
    }
  },

  // Filter students based on class and status
  filterStudents: function (turmaId, statusFilter) {
    const studentsContainer = document.querySelector('[data-dynamic="students-container"]')
    if (!studentsContainer) return

    // Clear the container
    studentsContainer.innerHTML = ""

    // Filter students
    let filteredStudents = this.students

    if (turmaId !== "todos") {
      filteredStudents = filteredStudents.filter((student) => student.class === turmaId)
    }

    if (statusFilter !== "todos") {
      const isActive = statusFilter === "ativo"
      filteredStudents = filteredStudents.filter((student) =>
        isActive ? student.attendance >= 75 : student.attendance < 75,
      )
    }

    // Add each student
    filteredStudents.forEach((student) => {
      const studentRow = document.createElement("tr")
      studentRow.innerHTML = `
                <td>
                    <div class="student-name">
                        <img src="${student.avatar}" alt="${student.name}">
                        <div>
                            <p>${student.name}</p>
                            <span class="text-muted">${this.classes.find((c) => c.id === student.class)?.name || ""}</span>
                        </div>
                    </div>
                </td>
                <td>${student.grades.math.average.toFixed(1)}</td>
                <td>${student.attendance}%</td>
                <td>
                    <span class="status-badge ${student.attendance >= 75 ? "active" : "inactive"}">
                        ${student.attendance >= 75 ? "Ativo" : "Inativo"}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <button class="action-btn view-student-btn" title="Ver Detalhes" data-student-id="${student.id}">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                        <button class="action-btn edit-student-btn" title="Editar" data-student-id="${student.id}">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button class="action-btn student-options-btn" title="Mais Opções" data-student-id="${student.id}">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </div>
                </td>
            `
      studentsContainer.appendChild(studentRow)
    })

    // Add event listeners to student buttons
    this.initializeStudentButtons()
  },

  // Initialize student action buttons
  initializeStudentButtons: () => {
    // View student details
    const viewStudentBtns = document.querySelectorAll(".view-student-btn")
    viewStudentBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        const studentId = this.getAttribute("data-student-id")
        alert(`Funcionalidade: Ver detalhes do aluno ${studentId}`)
        // Redirect to student details page or open modal
        // window.location.href = `aluno-detalhes.html?id=${studentId}`;
      })
    })

    // Edit student
    const editStudentBtns = document.querySelectorAll(".edit-student-btn")
    editStudentBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        const studentId = this.getAttribute("data-student-id")
        alert(`Funcionalidade: Editar aluno ${studentId}`)
        // Redirect to edit student page or open modal
        // window.location.href = `aluno-editar.html?id=${studentId}`;
      })
    })

    // Student options
    const studentOptionsBtns = document.querySelectorAll(".student-options-btn")
    studentOptionsBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        const studentId = this.getAttribute("data-student-id")
        alert(`Funcionalidade: Opções para o aluno ${studentId}`)
        // Show dropdown menu with options
      })
    })
  },

  // Initialize classes page
  initializeClassesPage: function () {
    // Check if we're on the classes page
    if (window.location.pathname.includes("turmas.html")) {
      // Load classes data
      this.loadClassesData()

      // Initialize view buttons
      const viewBtns = document.querySelectorAll(".view-btn")
      viewBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          viewBtns.forEach((b) => b.classList.remove("active"))
          this.classList.add("active")

          // Toggle between grid and list view
          const classesGrid = document.querySelector(".classes-grid")
          if (this.querySelector(".material-symbols-outlined").textContent === "view_list") {
            classesGrid.classList.add("list-view")
          } else {
            classesGrid.classList.remove("list-view")
          }
        })
      })

      // Initialize filter functionality
      const anoFilter = document.getElementById("ano-filter")
      const periodoFilter = document.getElementById("periodo-filter")

      if (anoFilter) {
        anoFilter.addEventListener("change", () => {
          // Filter classes by year
          alert("Funcionalidade: Filtrar turmas por ano letivo")
        })
      }

      if (periodoFilter) {
        periodoFilter.addEventListener("change", function () {
          // Filter classes by period
          const periodo = this.value
          window.dashboardData.loadClassesData(periodo)
        })
      }
    }
  },

  // Initialize class action buttons
  initializeClassButtons: () => {
    // View class details
    const classDetailsBtns = document.querySelectorAll(".class-details-btn")
    classDetailsBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        const classId = this.getAttribute("data-class-id")
        alert(`Funcionalidade: Ver detalhes da turma ${classId}`)
        // Redirect to class details page or open modal
        // window.location.href = `turma-detalhes.html?id=${classId}`;
      })
    })

    // View class students
    const classStudentsBtns = document.querySelectorAll(".class-students-btn")
    classStudentsBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        const classId = this.getAttribute("data-class-id")
        alert(`Funcionalidade: Ver alunos da turma ${classId}`)
        // Redirect to students page with class filter
        window.location.href = `alunos.html?turma=${classId}`
      })
    })
  },

  // Initialize grades page
  initializeGradesPage: () => {
    // Check if we're on the grades page
    if (window.location.pathname.includes("notas.html")) {
      // Initialize filter functionality
      const turmaFilter = document.getElementById("turma-filter")
      const periodoFilter = document.getElementById("periodo-filter")
      const disciplinaFilter = document.getElementById("disciplina-filter")

      if (turmaFilter && periodoFilter && disciplinaFilter) {
        // Load grades on filter change
        turmaFilter.addEventListener("change", () => {
          window.dashboardData.loadGrades()
        })

        periodoFilter.addEventListener("change", () => {
          window.dashboardData.loadGrades()
        })

        disciplinaFilter.addEventListener("change", () => {
          window.dashboardData.loadGrades()
        })
      }

      // Initialize save buttons
      const saveButtons = document.querySelectorAll(".btn-primary")
      saveButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          alert("Funcionalidade: Salvar alterações nas notas")
          // Save grades to server or local storage
        })
      })

      // Initialize export button
      const exportButton = document.querySelector(".btn-outline")
      if (exportButton) {
        exportButton.addEventListener("click", () => {
          alert("Funcionalidade: Exportar notas")
          // Export grades to CSV or Excel
        })
      }

      // Initialize recalculate button
      const recalculateButton = document.querySelector(".grades-actions .btn-outline")
      if (recalculateButton) {
        recalculateButton.addEventListener("click", () => {
          alert("Funcionalidade: Recalcular médias")
          // Recalculate all averages

          // Get all grade inputs
          const gradeInputs = document.querySelectorAll(".grade-input")
          gradeInputs.forEach((input) => {
            window.dashboardData.recalculateAverage(input)
          })
        })
      }
    }
  },

  // Initialize attendance page
  initializeAttendancePage: () => {
    // Check if we're on the attendance page
    if (window.location.pathname.includes("presenca.html")) {
      // Initialize filter functionality
      const turmaFilter = document.getElementById("turma-filter")
      const mesFilter = document.getElementById("mes-filter")

      if (turmaFilter) {
        turmaFilter.addEventListener("change", () => {
          window.dashboardData.loadAttendanceList()
        })
      }

      if (mesFilter) {
        mesFilter.addEventListener("change", () => {
          // Filter attendance by month
          alert("Funcionalidade: Filtrar presença por mês")
        })
      }

      // Initialize date navigation
      const prevDateBtn = document.getElementById("prev-date")
      const nextDateBtn = document.getElementById("next-date")

      if (prevDateBtn) {
        prevDateBtn.addEventListener("click", () => {
          alert("Funcionalidade: Navegar para o dia anterior")
          // Update date and attendance list
        })
      }

      if (nextDateBtn) {
        nextDateBtn.addEventListener("click", () => {
          alert("Funcionalidade: Navegar para o próximo dia")
          // Update date and attendance list
        })
      }

      // Initialize mark all present button
      const markAllPresentBtn = document.querySelector(".attendance-actions .btn-outline")
      if (markAllPresentBtn) {
        markAllPresentBtn.addEventListener("click", () => {
          document.querySelectorAll('input[value="present"]').forEach((input) => {
            input.checked = true
          })
        })
      }

      // Initialize save attendance button
      const saveAttendanceBtn = document.querySelector(".attendance-actions .btn-primary")
      if (saveAttendanceBtn) {
        saveAttendanceBtn.addEventListener("click", () => {
          alert("Funcionalidade: Salvar chamada")
          // Save attendance to server or local storage
        })
      }

      // Initialize export button
      const exportButton = document.querySelector(".header-actions .btn-outline")
      if (exportButton) {
        exportButton.addEventListener("click", () => {
          alert("Funcionalidade: Exportar registro de presença")
          // Export attendance to CSV or Excel
        })
      }
    }
  },

  // Initialize calendar page
  initializeCalendarPage: function () {
    // Check if we're on the calendar page
    if (window.location.pathname.includes("calendario.html")) {
      // Load calendar data
      this.loadCalendarData()

      // Initialize month navigation
      const prevMonthBtn = document.getElementById("prev-month")
      const nextMonthBtn = document.getElementById("next-month")

      if (prevMonthBtn) {
        prevMonthBtn.addEventListener("click", () => {
          alert("Funcionalidade: Mês anterior")
          // Update calendar to previous month
        })
      }

      if (nextMonthBtn) {
        nextMonthBtn.addEventListener("click", () => {
          alert("Funcionalidade: Próximo mês")
          // Update calendar to next month
        })
      }

      // Initialize view options
      const viewOptions = document.querySelectorAll(".view-options .btn-outline")
      viewOptions.forEach((btn) => {
        btn.addEventListener("click", function () {
          viewOptions.forEach((b) => b.classList.remove("active"))
          this.classList.add("active")

          // Change calendar view
          alert(`Funcionalidade: Alternar para visualização de ${this.textContent.trim()}`)
        })
      })

      // Initialize new event button
      const newEventBtn = document.querySelector(".page-header .btn-primary")
      if (newEventBtn) {
        newEventBtn.addEventListener("click", () => {
          alert("Funcionalidade: Adicionar novo evento")
          // Open new event modal
        })
      }

      // Initialize event filter
      const eventFilter = document.getElementById("event-filter")
      if (eventFilter) {
        eventFilter.addEventListener("change", function () {
          const eventType = this.value
          alert(`Funcionalidade: Filtrar eventos por tipo: ${eventType}`)
          // Filter events by type
        })
      }
    }
  },

  // Initialize calendar event listeners
  initializeCalendarEvents: () => {
    // Add click event to calendar events
    const calendarEvents = document.querySelectorAll(".calendar-event")
    calendarEvents.forEach((event) => {
      event.addEventListener("click", function () {
        const eventId = this.getAttribute("data-event-id")
        alert(`Funcionalidade: Ver detalhes do evento ${eventId}`)
        // Open event details modal
      })
    })
  },

  // Initialize materials page
  initializeMaterialsPage: () => {
    // Check if we're on the materials page
    if (window.location.pathname.includes("materiais.html")) {
      // Initialize filter functionality
      const turmaFilter = document.getElementById("turma-materiais-filter")
      const tipoFilter = document.getElementById("tipo-material-filter")
      const ordenarPor = document.getElementById("ordenar-por")

      if (turmaFilter) {
        turmaFilter.addEventListener("change", () => {
          alert("Funcionalidade: Filtrar materiais por turma")
          // Filter materials by class
        })
      }

      if (tipoFilter) {
        tipoFilter.addEventListener("change", () => {
          alert("Funcionalidade: Filtrar materiais por tipo")
          // Filter materials by type
        })
      }

      if (ordenarPor) {
        ordenarPor.addEventListener("change", () => {
          alert("Funcionalidade: Ordenar materiais")
          // Sort materials
        })
      }

      // Initialize tab buttons
      const tabBtns = document.querySelectorAll(".tab-btn")
      tabBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          tabBtns.forEach((b) => b.classList.remove("active"))
          this.classList.add("active")

          // Change materials view
          alert(`Funcionalidade: Alternar para ${this.textContent.trim()}`)
        })
      })

      // Initialize new material button
      const newMaterialBtn = document.querySelector(".page-header .btn-primary")
      if (newMaterialBtn) {
        newMaterialBtn.addEventListener("click", () => {
          alert("Funcionalidade: Adicionar novo material")
          // Open new material modal
        })
      }

      // Initialize material action buttons
      const materialActionBtns = document.querySelectorAll(".material-actions .action-btn")
      materialActionBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          const action = this.getAttribute("title")
          alert(`Funcionalidade: ${action} material`)
          // Perform action on material
        })
      })

      // Initialize upload card
      const uploadCard = document.querySelector(".upload-card")
      if (uploadCard) {
        uploadCard.addEventListener("click", () => {
          alert("Funcionalidade: Fazer upload de material")
          // Open file upload dialog
        })
      }

      // Initialize storage upgrade button
      const upgradeStorageBtn = document.querySelector(".storage-info .btn-outline")
      if (upgradeStorageBtn) {
        upgradeStorageBtn.addEventListener("click", () => {
          alert("Funcionalidade: Aumentar armazenamento")
          // Open storage upgrade options
        })
      }
    }
  },

  // Initialize messages page
  initializeMessagesPage: () => {
    // Check if we're on the messages page
    if (window.location.pathname.includes("mensagens.html")) {
      // Initialize new message button
      const newMessageBtn = document.querySelector(".page-header .btn-primary")
      if (newMessageBtn) {
        newMessageBtn.addEventListener("click", () => {
          alert("Funcionalidade: Nova mensagem")
          // Open new message modal
        })
      }

      // Initialize message action buttons
      const messageActionBtns = document.querySelectorAll(".message-actions .action-btn")
      messageActionBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
          const action = this.getAttribute("title")
          alert(`Funcionalidade: ${action} mensagem`)
          // Perform action on message
        })
      })

      // Initialize message items
      const messageItems = document.querySelectorAll(".message-item")
      messageItems.forEach((item) => {
        item.addEventListener("click", () => {
          alert("Funcionalidade: Abrir mensagem")
          // Open message details
        })
      })
    }
  },

  // Initialize reports page (boletins)
  initializeReportsPage: function () {
    // Check if we're on the reports page
    if (window.location.pathname.includes("boletins.html")) {
      // Initialize filter functionality
      const anoLetivoSelect = document.getElementById("ano-letivo")
      const periodoSelect = document.getElementById("periodo")
      const turmaSelect = document.getElementById("turma")

      // Update period options to show trimesters instead of bimesters
      if (periodoSelect) {
        // Clear existing options
        periodoSelect.innerHTML = ""

        // Add trimester options
        const options = [
          { value: "1", text: "1º Trimestre" },
          { value: "2", text: "2º Trimestre" },
          { value: "3", text: "3º Trimestre" },
          { value: "anual", text: "Anual" },
        ]

        options.forEach((option) => {
          const optionElement = document.createElement("option")
          optionElement.value = option.value
          optionElement.textContent = option.text
          if (option.value === "1") {
            optionElement.selected = true
            optionElement.setAttribute("data-dynamic", "current-period")
          }
          periodoSelect.appendChild(optionElement)
        })
      }

      if (anoLetivoSelect) {
        anoLetivoSelect.addEventListener("change", () => {
          alert("Funcionalidade: Alterar ano letivo do boletim")
          // Update report card year
        })
      }

      if (periodoSelect) {
        periodoSelect.addEventListener("change", () => {
          alert("Funcionalidade: Alterar período do boletim")
          // Update report card period
        })
      }

      if (turmaSelect) {
        turmaSelect.addEventListener("change", () => {
          this.loadStudentsList()
        })
      }

      // Initialize print options
      const printOptions = document.querySelectorAll(".print-options .toggle-input")
      printOptions.forEach((option) => {
        option.addEventListener("change", function () {
          const optionId = this.id
          const isChecked = this.checked
          alert(`Funcionalidade: ${isChecked ? "Incluir" : "Excluir"} ${optionId.replace("incluir-", "")}`)
          // Update report card preview
        })
      })

      // Initialize student selection
      const selectAllCheckbox = document.getElementById("selecionar-todos")
      if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
          const checkboxes = document.querySelectorAll(".alunos-list .toggle-input")
          checkboxes.forEach((checkbox) => {
            checkbox.checked = this.checked
          })
        })
      }

      // Initialize clear selection button
      const clearSelectionBtn = document.querySelector(".selection-header .btn-text")
      if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener("click", () => {
          const checkboxes = document.querySelectorAll(".alunos-list .toggle-input")
          checkboxes.forEach((checkbox) => {
            checkbox.checked = false
          })

          // Update select all checkbox
          const selectAllCheckbox = document.getElementById("selecionar-todos")
          if (selectAllCheckbox) {
            selectAllCheckbox.checked = false
          }
        })
      }

      // Initialize preview buttons
      const visualizarBtn = document.getElementById("visualizarBtn")
      if (visualizarBtn) {
        visualizarBtn.addEventListener("click", () => {
          // Get all selected students
          const selectedCheckboxes = document.querySelectorAll(".alunos-list .toggle-input:checked")
          if (selectedCheckboxes.length > 0) {
            // Get the first selected student for preview
            const studentId = selectedCheckboxes[0].getAttribute("data-student-id")
            this.loadStudentReportCard(studentId)

            // Show message about multiple students
            if (selectedCheckboxes.length > 1) {
              alert(
                `Visualizando boletim do primeiro aluno selecionado. Ao imprimir, todos os ${selectedCheckboxes.length} alunos selecionados serão incluídos.`,
              )
            }
          } else {
            alert("Selecione pelo menos um aluno para visualizar o boletim.")
          }
        })
      }

      // Initialize export button
      const exportarBtn = document.getElementById("exportarBtn")
      if (exportarBtn) {
        exportarBtn.addEventListener("click", () => {
          // Get all selected students
          const selectedCheckboxes = document.querySelectorAll(".alunos-list .toggle-input:checked")
          if (selectedCheckboxes.length > 0) {
            alert(`Funcionalidade: Exportar boletins de ${selectedCheckboxes.length} aluno(s) para PDF`)
          } else {
            alert("Selecione pelo menos um aluno para exportar o boletim.")
          }
        })
      }

      // Initialize print button
      const printButton = document.getElementById("printButton")
      if (printButton) {
        printButton.addEventListener("click", () => {
          // Get all selected students
          const selectedCheckboxes = document.querySelectorAll(".alunos-list .toggle-input:checked")
          if (selectedCheckboxes.length > 0) {
            alert(`Preparando para imprimir boletins de ${selectedCheckboxes.length} aluno(s)`)
            // In a real implementation, we would generate all boletins and then print them
            window.print()
          } else {
            alert("Selecione pelo menos um aluno para imprimir o boletim.")
          }
        })
      }

      // Initialize zoom buttons
      const zoomInBtn = document.getElementById("zoomInBtn")
      const zoomOutBtn = document.getElementById("zoomOutBtn")

      if (zoomInBtn) {
        zoomInBtn.addEventListener("click", () => {
          let currentZoom =
            Number.parseInt(
              document.getElementById("boletim-document").style.transform.replace("scale(", "").replace(")", "") || 1,
            ) * 100
          if (currentZoom < 150) {
            currentZoom += 10
            document.getElementById("boletim-document").style.transform = `scale(${currentZoom / 100})`
            document.getElementById("boletim-document").style.transformOrigin = "top center"
          }
        })
      }

      if (zoomOutBtn) {
        zoomOutBtn.addEventListener("click", () => {
          let currentZoom =
            Number.parseInt(
              document.getElementById("boletim-document").style.transform.replace("scale(", "").replace(")", "") || 1,
            ) * 100
          if (currentZoom > 50) {
            currentZoom -= 10
            document.getElementById("boletim-document").style.transform = `scale(${currentZoom / 100})`
            document.getElementById("boletim-document").style.transformOrigin = "top center"
          }
        })
      }

      // Initialize fullscreen button
      const fullscreenBtn = document.getElementById("fullscreenBtn")
      if (fullscreenBtn) {
        fullscreenBtn.addEventListener("click", () => {
          const previewContent = document.querySelector(".preview-content")
          if (previewContent.requestFullscreen) {
            previewContent.requestFullscreen()
          } else if (previewContent.webkitRequestFullscreen) {
            previewContent.webkitRequestFullscreen()
          } else if (previewContent.msRequestFullscreen) {
            previewContent.msRequestFullscreen()
          }
        })
      }

      // Load students list
      this.loadStudentsList()
    }
  },

  // Load students list for report cards
  loadStudentsList: function () {
    const alunosList = document.getElementById("alunos-list")
    if (!alunosList) return

    // Clear the list
    alunosList.innerHTML = ""

    // Get the selected class
    const turmaSelect = document.getElementById("turma")
    const turmaId = turmaSelect ? turmaSelect.value : "todos"

    // Filter students by class
    let filteredStudents = this.students
    if (turmaId !== "todos") {
      filteredStudents = filteredStudents.filter((student) => student.class === turmaId)
    }

    // Add each student to the list
    filteredStudents.forEach((student, index) => {
      const alunoItem = document.createElement("div")
      alunoItem.className = "aluno-item"
      alunoItem.innerHTML = `
        <div class="toggle-switch">
          <input type="checkbox" id="aluno-${index}" class="toggle-input" checked data-student-id="${student.id}">
          <label for="aluno-${index}" class="toggle-label"></label>
          <div class="aluno-info">
            <img src="${student.avatar}" alt="${student.name}">
            <div>
              <p>${student.name}</p>
              <span class="text-muted">${this.classes.find((c) => c.id === student.class)?.name || ""}</span>
            </div>
          </div>
        </div>
      `
      alunosList.appendChild(alunoItem)
    })
  },

  // Load student report card
  loadStudentReportCard: function (studentId) {
    const student = this.students.find((s) => s.id === studentId)
    if (!student) return

    // Update student information in the report card
    const studentNameElement = document.querySelector('[data-dynamic="report-student-name"]')
    if (studentNameElement) studentNameElement.textContent = student.name

    const studentClassElement = document.querySelector('[data-dynamic="report-student-class"]')
    if (studentClassElement) {
      const classInfo = this.classes.find((c) => c.id === student.class)
      studentClassElement.textContent = classInfo ? classInfo.name : ""
    }

    // Update comments
    const commentsElement = document.querySelector('[data-dynamic="report-comments"]')
    if (commentsElement) commentsElement.textContent = student.comments

    // Update grades table
    this.updateGradesTable(student)

    // Update performance chart
    this.updatePerformanceChart(student)
  },

  // Initialize settings page
  initializeSettingsPage: () => {
    // Check if we're on the settings page
    if (window.location.pathname.includes("configuracoes.html")) {
      // Initialize settings navigation
      const settingsNavItems = document.querySelectorAll(".settings-nav-item")
      settingsNavItems.forEach((item) => {
        item.addEventListener("click", function (e) {
          e.preventDefault()

          // Remove active class from all items
          settingsNavItems.forEach((navItem) => {
            navItem.classList.remove("active")
          })

          // Add active class to clicked item
          this.classList.add("active")

          // Show corresponding section
          const targetId = this.getAttribute("href").substring(1)
          document.querySelectorAll(".settings-section").forEach((section) => {
            section.style.display = "none"
          })
          document.getElementById(targetId).style.display = "block"
        })
      })

      // Initialize profile picture actions
      const changePictureBtn = document.querySelector(".profile-picture-actions .btn-outline")
      if (changePictureBtn) {
        changePictureBtn.addEventListener("click", () => {
          alert("Funcionalidade: Alterar foto de perfil")
          // Open file upload dialog
        })
      }

      // Initialize remove picture button
      const removePictureBtn = document.querySelector(".profile-picture-actions .btn-text")
      if (removePictureBtn) {
        removePictureBtn.addEventListener("click", () => {
          alert("Funcionalidade: Remover foto de perfil")
          // Remove profile picture
        })
      }

      // Initialize save changes button
      const saveChangesBtn = document.querySelector(".page-header .btn-primary")
      if (saveChangesBtn) {
        saveChangesBtn.addEventListener("click", () => {
          alert("Funcionalidade: Salvar alterações nas configurações")
          // Save settings
        })
      }

      // Initialize password change button
      const changePasswordBtn = document.querySelector(".account-settings .btn-primary")
      if (changePasswordBtn) {
        changePasswordBtn.addEventListener("click", () => {
          alert("Funcionalidade: Alterar senha")
          // Change password
        })
      }

      // Initialize two-factor setup button
      const twoFactorSetupBtn = document.querySelector(".account-settings .btn-outline")
      if (twoFactorSetupBtn) {
        twoFactorSetupBtn.addEventListener("click", () => {
          alert("Funcionalidade: Configurar verificação em duas etapas")
          // Setup two-factor authentication
        })
      }

      // Initialize deactivate account button
      const deactivateAccountBtn = document.querySelector(".danger-zone .btn-danger")
      if (deactivateAccountBtn) {
        deactivateAccountBtn.addEventListener("click", () => {
          alert("Funcionalidade: Desativar conta")
          // Deactivate account
        })
      }

      // Initialize theme options
      const themeOptions = document.querySelectorAll('input[name="theme"]')
      themeOptions.forEach((option) => {
        option.addEventListener("change", function () {
          const theme = this.id.replace("theme-", "")
          alert(`Funcionalidade: Alterar tema para ${theme}`)
          // Change theme
        })
      })

      // Initialize color options
      const colorOptions = document.querySelectorAll('input[name="color"]')
      colorOptions.forEach((option) => {
        option.addEventListener("change", function () {
          const color = this.id.replace("color-", "")
          alert(`Funcionalidade: Alterar cor de destaque para ${color}`)
          // Change accent color
        })
      })

      // Initialize font options
      const fontFamilySelect = document.getElementById("font-family")
      const fontSizeSelect = document.getElementById("font-size")

      if (fontFamilySelect) {
        fontFamilySelect.addEventListener("change", function () {
          alert(`Funcionalidade: Alterar família da fonte para ${this.value}`)
          // Change font family
        })
      }

      if (fontSizeSelect) {
        fontSizeSelect.addEventListener("change", function () {
          alert(`Funcionalidade: Alterar tamanho da fonte para ${this.value}`)
          // Change font size
        })
      }

      // Initialize privacy options
      const privacySelects = document.querySelectorAll(".privacy-settings select")
      privacySelects.forEach((select) => {
        select.addEventListener("change", () => {
          alert(`Funcionalidade: Alterar configuração de privacidade`)
          // Change privacy setting
        })
      })

      // Initialize cookie management button
      const manageCookiesBtn = document.querySelector(".privacy-settings .btn-outline:nth-of-type(1)")
      if (manageCookiesBtn) {
        manageCookiesBtn.addEventListener("click", () => {
          alert("Funcionalidade: Gerenciar cookies")
          // Open cookie management
        })
      }

      // Initialize export data button
      const exportDataBtn = document.querySelector(".privacy-settings .btn-outline:nth-of-type(2)")
      if (exportDataBtn) {
        exportDataBtn.addEventListener("click", () => {
          alert("Funcionalidade: Exportar meus dados")
          // Export user data
        })
      }

      // Initialize add integration button
      const addIntegrationBtn = document.querySelector(".integration-settings .btn-primary")
      if (addIntegrationBtn) {
        addIntegrationBtn.addEventListener("click", () => {
          alert("Funcionalidade: Adicionar nova integração")
          // Open add integration dialog
        })
      }

      // Initialize disconnect app buttons
      const disconnectAppBtns = document.querySelectorAll(".connected-app .btn-outline")
      disconnectAppBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
          alert("Funcionalidade: Desconectar aplicativo")
          // Disconnect app
        })
      })

      // Initialize export configuration button
      const configureExportBtn = document.querySelector(".integration-settings .btn-outline:last-child")
      if (configureExportBtn) {
        configureExportBtn.addEventListener("click", () => {
          alert("Funcionalidade: Configurar exportação")
          // Configure data export
        })
      }
    }
  },

  // Initialize mini pauta page
  initializeMiniPautaPage: function () {
    // Check if we're on the mini pauta page
    if (window.location.pathname.includes("minipauta.html")) {
      // Initialize filter functionality
      const anoLetivoSelect = document.getElementById("ano-letivo")
      const periodoSelect = document.getElementById("periodo")
      const turmaSelect = document.getElementById("turma")
      const disciplinaSelect = document.getElementById("disciplina")

      // Update period options to show trimesters instead of bimesters
      if (periodoSelect) {
        // Clear existing options
        periodoSelect.innerHTML = ""

        // Add trimester options
        const options = [
          { value: "1", text: "1º Trimestre" },
          { value: "2", text: "2º Trimestre" },
          { value: "3", text: "3º Trimestre" },
          { value: "anual", text: "Anual" },
        ]

        options.forEach((option) => {
          const optionElement = document.createElement("option")
          optionElement.value = option.value
          optionElement.textContent = option.text
          if (option.value === "1") {
            optionElement.selected = true
            optionElement.setAttribute("data-dynamic", "current-period")
          }
          periodoSelect.appendChild(optionElement)
        })
      }

      if (anoLetivoSelect) {
        anoLetivoSelect.addEventListener("change", () => {
          alert("Funcionalidade: Alterar ano letivo da minipauta")
          // Update mini pauta year
        })
      }

      if (periodoSelect) {
        periodoSelect.addEventListener("change", () => {
          alert("Funcionalidade: Alterar período da minipauta")
          // Update mini pauta period
        })
      }

      if (turmaSelect) {
        turmaSelect.addEventListener("change", () => {
          this.loadMinipautaData()
        })
      }

      if (disciplinaSelect) {
        disciplinaSelect.addEventListener("change", () => {
          this.loadMinipautaData()
        })
      }

      // Initialize display options
      const displayOptions = document.querySelectorAll(".option-group .toggle-input")
      displayOptions.forEach((option) => {
        option.addEventListener("change", function () {
          const optionId = this.id
          const isChecked = this.checked
          alert(`Funcionalidade: ${isChecked ? "Incluir" : "Excluir"} ${optionId.replace("incluir-", "")}`)
          // Update mini pauta preview
        })
      })

      // Initialize preview button
      const visualizarBtn = document.getElementById("visualizarBtn")
      if (visualizarBtn) {
        visualizarBtn.addEventListener("click", () => {
          this.loadMinipautaData()
        })
      }

      // Initialize export button
      const exportarBtn = document.getElementById("exportarBtn")
      if (exportarBtn) {
        exportarBtn.addEventListener("click", () => {
          alert("Funcionalidade: Exportar minipauta para PDF")
          // Export mini pauta to PDF
        })
      }

      // Initialize print button
      const printButton = document.getElementById("printButton")
      if (printButton) {
        printButton.addEventListener("click", () => {
          window.print()
        })
      }

      // Initialize zoom buttons
      const zoomInBtn = document.getElementById("zoomInBtn")
      const zoomOutBtn = document.getElementById("zoomOutBtn")

      if (zoomInBtn) {
        zoomInBtn.addEventListener("click", () => {
          let currentZoom =
            Number.parseInt(
              document.getElementById("minipauta-document").style.transform.replace("scale(", "").replace(")", "") || 1,
            ) * 100
          if (currentZoom < 150) {
            currentZoom += 10
            document.getElementById("minipauta-document").style.transform = `scale(${currentZoom / 100})`
            document.getElementById("minipauta-document").style.transformOrigin = "top center"
          }
        })
      }

      if (zoomOutBtn) {
        zoomOutBtn.addEventListener("click", () => {
          let currentZoom =
            Number.parseInt(
              document.getElementById("minipauta-document").style.transform.replace("scale(", "").replace(")", "") || 1,
            ) * 100
          if (currentZoom > 50) {
            currentZoom -= 10
            document.getElementById("minipauta-document").style.transform = `scale(${currentZoom / 100})`
            document.getElementById("minipauta-document").style.transformOrigin = "top center"
          }
        })
      }

      // Initialize fullscreen button
      const fullscreenBtn = document.getElementById("fullscreenBtn")
      if (fullscreenBtn) {
        fullscreenBtn.addEventListener("click", () => {
          const previewContent = document.querySelector(".preview-content")
          if (previewContent.requestFullscreen) {
            previewContent.requestFullscreen()
          } else if (previewContent.webkitRequestFullscreen) {
            previewContent.webkitRequestFullscreen()
          } else if (previewContent.msRequestFullscreen) {
            previewContent.msRequestFullscreen()
          }
        })
      }

      // Load minipauta data
      this.loadMinipautaData()
    }
  },

  // Load minipauta data
  loadMinipautaData: function () {
    const turmaId = document.getElementById("turma")?.value || "9A"
    const disciplinaId = document.getElementById("disciplina")?.value || "math"

    // Update titles
    const turmaNomeElement = document.getElementById("turma-nome")
    if (turmaNomeElement) {
      const turmaInfo = this.classes.find((c) => c.id === turmaId)
      turmaNomeElement.textContent = turmaInfo ? turmaInfo.name : turmaId
    }

    const disciplinaTitleElement = document.getElementById("disciplina-title")
    if (disciplinaTitleElement && document.getElementById("disciplina")) {
      disciplinaTitleElement.textContent =
        document.getElementById("disciplina").options[document.getElementById("disciplina").selectedIndex].text
    }

    const totalAlunosElement = document.getElementById("total-alunos")
    if (totalAlunosElement) {
      const turmaInfo = this.classes.find((c) => c.id === turmaId)
      totalAlunosElement.textContent = turmaInfo ? turmaInfo.students : 0
    }

    // Filter students by class
    const filteredStudents = this.students.filter((student) => student.class === turmaId)

    // Update table
    const minipautaTableBody = document.getElementById("minipauta-table-body")
    if (minipautaTableBody) {
      // Clear table
      minipautaTableBody.innerHTML = ""

      // Add each student
      filteredStudents.forEach((student, index) => {
        const row = document.createElement("tr")

        // Get grades for the selected subject
        const grades = student.grades[disciplinaId]
        if (grades) {
          // Determine situation based on average
          let situacao = "Aprovado"
          let situacaoClass = "approved"

          if (grades.average < 6.0) {
            situacao = "Reprovado"
            situacaoClass = "failed"
          } else if (grades.average < 7.0) {
            situacao = "Recuperação"
            situacaoClass = "recovery"
          }

          row.innerHTML = `
            <td>${index + 1}</td>
            <td class="student-name">${student.name}</td>
            <td class="grade-cell">${grades.av1.toFixed(1)}</td>
            <td class="grade-cell">${grades.av2.toFixed(1)}</td>
            <td class="grade-cell">${grades.av3.toFixed(1)}</td>
            <td class="grade-cell">${grades.average.toFixed(1)}</td>
            <td class="attendance-cell">${student.attendance}%</td>
            <td class="grade-status ${situacaoClass}">${situacao}</td>
          `

          minipautaTableBody.appendChild(row)
        }
      })
    }
  },

  // Initialize performance chart on dashboard
  initializePerformanceChart: () => {
    // This is a placeholder for chart initialization
    // In a real application, you would use a charting library like Chart.js
    console.log("Performance chart initialized")
  },

  // Update recent activities on dashboard
  updateRecentActivities: () => {
    const recentActivitiesContainer = document.querySelector(".recent-activities-list")
    if (!recentActivitiesContainer) return

    // Sample recent activities
    const activities = [
      {
        type: "grade",
        description: "Notas lançadas para 9º Ano A",
        time: "2 horas atrás",
      },
      {
        type: "attendance",
        description: "Chamada realizada para 8º Ano B",
        time: "3 horas atrás",
      },
      {
        type: "material",
        description: 'Material "Apostila de Álgebra" adicionado',
        time: "5 horas atrás",
      },
      {
        type: "message",
        description: "Mensagem enviada para Coordenação",
        time: "1 dia atrás",
      },
    ]

    // Clear the container
    recentActivitiesContainer.innerHTML = ""

    // Add each activity
    activities.forEach((activity) => {
      const activityItem = document.createElement("div")
      activityItem.className = "activity-item"

      let icon = ""
      switch (activity.type) {
        case "grade":
          icon = "grade"
          break
        case "attendance":
          icon = "fact_check"
          break
        case "material":
          icon = "book"
          break
        case "message":
          icon = "chat"
          break
        default:
          icon = "info"
      }

      activityItem.innerHTML = `
                <div class="activity-icon">
                    <span class="material-symbols-outlined">${icon}</span>
                </div>
                <div class="activity-details">
                    <p>${activity.description}</p>
                    <span class="activity-time">${activity.time}</span>
                </div>
            `

      recentActivitiesContainer.appendChild(activityItem)
    })
  },

  // Functions from the provided botoes.js file
  initializeButtonsAndInteractions: function () {
    // Initialize menu toggle
    this.initializeMenuToggle()

    // Initialize search bar
    this.initializeSearchBar()

    // Initialize notifications
    this.initializeNotifications()

    // Initialize action buttons
    this.initializeActionButtons()

    // Initialize page-specific buttons
    this.initializePageSpecificButtons()
  },

  initializeMenuToggle: () => {
    const menuToggle = document.getElementById("menuToggle")
    if (menuToggle) {
      menuToggle.addEventListener("click", () => {
        document.querySelector(".sidebar").classList.toggle("collapsed")
        document.querySelector(".content").classList.toggle("expanded")
      })
    }
  },

  initializeSearchBar: () => {
    const searchInput = document.querySelector(".search-container input")
    if (searchInput) {
      searchInput.addEventListener("focus", function () {
        this.parentElement.classList.add("focused")
      })

      searchInput.addEventListener("blur", function () {
        this.parentElement.classList.remove("focused")
      })

      searchInput.addEventListener("keyup", function (e) {
        if (e.key === "Enter") {
          // Simulate search
          alert(`Pesquisando por: ${this.value}`)
          this.value = ""
        }
      })
    }
  },

  initializeNotifications: () => {
    const notificationIcon = document.querySelector(".notification")
    if (notificationIcon) {
      notificationIcon.addEventListener("click", () => {
        alert("Você tem 3 notificações não lidas.")
      })
    }

    const helpIcon = document.querySelector(".top-bar-actions .material-symbols-outlined:last-child")
    if (helpIcon) {
      helpIcon.addEventListener("click", () => {
        alert("Centro de Ajuda: Para suporte, entre em contato com o administrador do sistema.")
      })
    }
  },

  initializeActionButtons: () => {
    // Buttons with add icon
    const addButtons = document.querySelectorAll(".btn-primary")
    addButtons.forEach((btn) => {
      const icon = btn.querySelector(".material-symbols-outlined")
      if (icon && icon.textContent === "add") {
        btn.addEventListener("click", function () {
          const buttonText = this.textContent.trim().replace("add", "").trim()
          alert(`Adicionar ${buttonText}`)
        })
      }
    })

    // Export buttons
    const exportButtons = document.querySelectorAll(".btn-outline")
    exportButtons.forEach((btn) => {
      const icon = btn.querySelector(".material-symbols-outlined")
      if (icon && icon.textContent === "download") {
        btn.addEventListener("click", () => {
          alert("Exportando dados...")
        })
      }
    })

    // Save buttons
    const saveButtons = document.querySelectorAll(".btn-primary")
    saveButtons.forEach((btn) => {
      const icon = btn.querySelector(".material-symbols-outlined")
      if (icon && icon.textContent === "save") {
        btn.addEventListener("click", () => {
          alert("Alterações salvas com sucesso!")
        })
      }
    })

    // Action buttons in tables
    const actionButtons = document.querySelectorAll(".action-btn")
    actionButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const action = this.getAttribute("title") || "Ação"
        const row = this.closest("tr")
        let targetName = "item"

        if (row) {
          const nameElement = row.querySelector(".student-name p") || row.querySelector(".student-name")
          if (nameElement) {
            targetName = nameElement.textContent.trim()
          }
        }

        alert(`${action} para ${targetName}`)
      })
    })
  },

  initializePageSpecificButtons: function () {
    // Check which page we're on
    const currentPage = window.location.pathname.split("/").pop()

    switch (currentPage) {
      case "boletins.html":
        this.initializeBoletinsButtons()
        break
      case "minipauta.html":
        this.initializeMinipautasButtons()
        break
      case "notas.html":
        this.initializeNotasButtons()
        break
      case "presenca.html":
        this.initializePresencaButtons()
        break
      case "calendario.html":
        this.initializeCalendarioButtons()
        break
      case "mensagens.html":
        this.initializeMensagensButtons()
        break
      default:
        // Generic page or dashboard
        break
    }
  },

  initializeBoletinsButtons: () => {
    // Print button
    const printButton = document.getElementById("printButton")
    if (printButton) {
      printButton.addEventListener("click", () => {
        window.print()
      })
    }

    // Preview button
    const visualizarBtn = document.getElementById("visualizarBtn")
    if (visualizarBtn) {
      visualizarBtn.addEventListener("click", () => {
        // Get all selected students
        const selectedCheckboxes = document.querySelectorAll(".alunos-list .toggle-input:checked")
        if (selectedCheckboxes.length > 0) {
          const studentId = selectedCheckboxes[0].getAttribute("data-student-id")
          window.dashboardData.loadStudentReportCard(studentId)
          alert("Boletim atualizado com os dados do aluno selecionado.")

          if (selectedCheckboxes.length > 1) {
            alert(`Ao imprimir, todos os ${selectedCheckboxes.length} alunos selecionados serão incluídos.`)
          }
        } else {
          alert("Por favor, selecione pelo menos um aluno.")
        }
      })
    }

    // Export PDF button
    const exportarBtn = document.getElementById("exportarBtn")
    if (exportarBtn) {
      exportarBtn.addEventListener("click", () => {
        alert("Exportando boletim como PDF...")
      })
    }

    // Zoom buttons
    const zoomInBtn = document.getElementById("zoomInBtn")
    const zoomOutBtn = document.getElementById("zoomOutBtn")
    let currentZoom = 100

    if (zoomInBtn) {
      zoomInBtn.addEventListener("click", () => {
        if (currentZoom < 150) {
          currentZoom += 10
          document.getElementById("boletim-document").style.transform = `scale(${currentZoom / 100})`
          document.getElementById("boletim-document").style.transformOrigin = "top center"
        }
      })
    }

    if (zoomOutBtn) {
      zoomOutBtn.addEventListener("click", () => {
        if (currentZoom > 50) {
          currentZoom -= 10
          document.getElementById("boletim-document").style.transform = `scale(${currentZoom / 100})`
          document.getElementById("boletim-document").style.transformOrigin = "top center"
        }
      })
    }

    // Fullscreen button
    const fullscreenBtn = document.getElementById("fullscreenBtn")
    if (fullscreenBtn) {
      fullscreenBtn.addEventListener("click", () => {
        const previewContent = document.querySelector(".preview-content")
        if (previewContent.requestFullscreen) {
          previewContent.requestFullscreen()
        } else if (previewContent.webkitRequestFullscreen) {
          previewContent.webkitRequestFullscreen()
        } else if (previewContent.msRequestFullscreen) {
          previewContent.msRequestFullscreen()
        }
      })
    }

    // Select all checkbox
    const selecionarTodos = document.getElementById("selecionar-todos")
    if (selecionarTodos) {
      selecionarTodos.addEventListener("change", function () {
        const checkboxes = document.querySelectorAll(".alunos-list .toggle-input")
        checkboxes.forEach((checkbox) => {
          checkbox.checked = this.checked
        })
      })
    }

    // Clear selection button
    const limparSelecaoBtn = document.querySelector(".selection-header .btn-text")
    if (limparSelecaoBtn) {
      limparSelecaoBtn.addEventListener("click", () => {
        const checkboxes = document.querySelectorAll(".alunos-list .toggle-input")
        checkboxes.forEach((checkbox) => {
          checkbox.checked = false
        })
        document.getElementById("selecionar-todos").checked = false
      })
    }
  },

  initializeMinipautasButtons: () => {
    // Implementation for minipauta buttons
    // This would be similar to the boletins buttons but specific to minipautas
  },

  initializeNotasButtons: () => {
    // Implementation for notas buttons
  },

  initializePresencaButtons: () => {
    // Implementation for presenca buttons
  },

  initializeCalendarioButtons: () => {
    // Implementation for calendario buttons
  },

  initializeMensagensButtons: () => {
    // Implementation for mensagens buttons
  },
}

// Initialize everything when the DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.dashboardData.initializeAll()
})
