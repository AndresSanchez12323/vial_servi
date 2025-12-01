# Soluciones de Ecuaciones Diferenciales por Coeficientes Indeterminados (Método de Superposición)

## Problemas 1-26

---

**Problema 1:** $y'' + 3y' + 2y = 6$

$r^2 + 3r + 2 = 0 \Rightarrow (r+1)(r+2) = 0 \Rightarrow r_1 = -1, r_2 = -2$

$y_c = C_1 e^{-x} + C_2 e^{-2x}$

$y_p = A \Rightarrow y_p' = 0, y_p'' = 0 \Rightarrow 2A = 6 \Rightarrow A = 3$

$\boxed{y = C_1 e^{-x} + C_2 e^{-2x} + 3}$

---

**Problema 2:** $4y'' + 9y = 15$

$4r^2 + 9 = 0 \Rightarrow r = \pm\frac{3i}{2}$

$y_c = C_1 \cos\frac{3x}{2} + C_2 \sin\frac{3x}{2}$

$y_p = A \Rightarrow y_p'' = 0 \Rightarrow 9A = 15 \Rightarrow A = \frac{5}{3}$

$\boxed{y = C_1 \cos\frac{3x}{2} + C_2 \sin\frac{3x}{2} + \frac{5}{3}}$

---

**Problema 3:** $y'' - 10y' + 25y = 30x + 3$

$r^2 - 10r + 25 = 0 \Rightarrow (r-5)^2 = 0 \Rightarrow r = 5$ (doble)

$y_c = C_1 e^{5x} + C_2 x e^{5x}$

$y_p = Ax + B \Rightarrow y_p' = A, y_p'' = 0 \Rightarrow -10A + 25(Ax + B) = 30x + 3$

$25A = 30 \Rightarrow A = \frac{6}{5}$, $-10A + 25B = 3 \Rightarrow B = \frac{3 + 12}{25} = \frac{15}{25} = \frac{3}{5}$

$\boxed{y = C_1 e^{5x} + C_2 x e^{5x} + \frac{6}{5}x + \frac{3}{5}}$

---

**Problema 4:** $y'' + y' - 6y = 2x$

$r^2 + r - 6 = 0 \Rightarrow (r+3)(r-2) = 0 \Rightarrow r_1 = -3, r_2 = 2$

$y_c = C_1 e^{-3x} + C_2 e^{2x}$

$y_p = Ax + B \Rightarrow y_p' = A, y_p'' = 0 \Rightarrow A - 6Ax - 6B = 2x$

$-6A = 2 \Rightarrow A = -\frac{1}{3}$, $A - 6B = 0 \Rightarrow B = -\frac{1}{18}$

$\boxed{y = C_1 e^{-3x} + C_2 e^{2x} - \frac{1}{3}x - \frac{1}{18}}$

---

**Problema 5:** $\frac{1}{4}y'' + y' + y = x^2 - 2x$

$\frac{1}{4}r^2 + r + 1 = 0 \Rightarrow r^2 + 4r + 4 = 0 \Rightarrow (r+2)^2 = 0 \Rightarrow r = -2$ (doble)

$y_c = C_1 e^{-2x} + C_2 x e^{-2x}$

$y_p = Ax^2 + Bx + C \Rightarrow y_p' = 2Ax + B, y_p'' = 2A$

$\frac{1}{4}(2A) + (2Ax + B) + (Ax^2 + Bx + C) = x^2 - 2x$

$Ax^2 + (2A + B)x + (\frac{A}{2} + B + C) = x^2 - 2x$

$A = 1$, $2A + B = -2 \Rightarrow B = -4$, $\frac{1}{2} - 4 + C = 0 \Rightarrow C = \frac{7}{2}$

$\boxed{y = C_1 e^{-2x} + C_2 x e^{-2x} + x^2 - 4x + \frac{7}{2}}$

---

**Problema 6:** $y'' - 8y' + 20y = 100x^2 - 26xe^x$

$r^2 - 8r + 20 = 0 \Rightarrow r = \frac{8 \pm \sqrt{64-80}}{2} = 4 \pm 2i$

$y_c = e^{4x}(C_1 \cos 2x + C_2 \sin 2x)$

$y_{p_1} = Ax^2 + Bx + C$ para $100x^2$

$y_{p_1}'' - 8y_{p_1}' + 20y_{p_1} = 2A - 8(2Ax + B) + 20(Ax^2 + Bx + C) = 100x^2$

$20A = 100 \Rightarrow A = 5$, $-16A + 20B = 0 \Rightarrow B = 4$, $2A - 8B + 20C = 0 \Rightarrow C = \frac{22}{20} = \frac{11}{10}$

$y_{p_2} = (Dx + E)e^x$ para $-26xe^x$

$y_{p_2}' = (Dx + D + E)e^x$, $y_{p_2}'' = (Dx + 2D + E)e^x$

$(Dx + 2D + E - 8Dx - 8D - 8E + 20Dx + 20E)e^x = -26xe^x$

$13Dx + (2D - 8D + E - 8E + 20E) = -26x$

$13D = -26 \Rightarrow D = -2$, $-6D + 13E = 0 \Rightarrow E = -\frac{12}{13}$

$\boxed{y = e^{4x}(C_1 \cos 2x + C_2 \sin 2x) + 5x^2 + 4x + \frac{11}{10} + \left(-2x - \frac{12}{13}\right)e^x}$

---

**Problema 7:** $y'' + 3y = -48x^2 e^{2x}$

$r^2 + 3 = 0 \Rightarrow r = \pm\sqrt{3}i$

$y_c = C_1 \cos\sqrt{3}x + C_2 \sin\sqrt{3}x$

$y_p = (Ax^2 + Bx + C)e^{2x}$

$y_p' = (2Ax + B)e^{2x} + 2(Ax^2 + Bx + C)e^{2x} = (2Ax^2 + (2A + 2B)x + B + 2C)e^{2x}$

$y_p'' = (4Ax^2 + (8A + 4B)x + 2A + 4B + 4C)e^{2x}$

$(4A + 3A)x^2 e^{2x} + (8A + 4B + 3(2A + 2B))x e^{2x} + (2A + 4B + 4C + 3B + 6C)e^{2x} = -48x^2 e^{2x}$

$7Ax^2 + (8A + 4B + 6A + 6B)x + (2A + 7B + 10C) = -48x^2$

$7A = -48 \Rightarrow A = -\frac{48}{7}$, $14A + 10B = 0 \Rightarrow B = \frac{96}{10} = \frac{48}{5}$

$2A + 7B + 10C = 0 \Rightarrow -\frac{96}{7} + \frac{336}{5} + 10C = 0 \Rightarrow C = \frac{96/7 - 336/5}{10} = \frac{480 - 2352}{350} = -\frac{1872}{350} = -\frac{936}{175}$

$\boxed{y = C_1 \cos\sqrt{3}x + C_2 \sin\sqrt{3}x + \left(-\frac{48}{7}x^2 + \frac{48}{5}x - \frac{936}{175}\right)e^{2x}}$

---

**Problema 8:** $4y'' - 4y' - 3y = \cos 2x$

$4r^2 - 4r - 3 = 0 \Rightarrow r = \frac{4 \pm \sqrt{16+48}}{8} = \frac{4 \pm 8}{8} \Rightarrow r_1 = \frac{3}{2}, r_2 = -\frac{1}{2}$

$y_c = C_1 e^{3x/2} + C_2 e^{-x/2}$

$y_p = A\cos 2x + B\sin 2x$

$y_p' = -2A\sin 2x + 2B\cos 2x$, $y_p'' = -4A\cos 2x - 4B\sin 2x$

$4(-4A\cos 2x - 4B\sin 2x) - 4(-2A\sin 2x + 2B\cos 2x) - 3(A\cos 2x + B\sin 2x) = \cos 2x$

$(-16A - 8B - 3A)\cos 2x + (-16B + 8A - 3B)\sin 2x = \cos 2x$

$-19A - 8B = 1$, $8A - 19B = 0 \Rightarrow B = \frac{8A}{19}$

$-19A - \frac{64A}{19} = 1 \Rightarrow -\frac{361A + 64A}{19} = 1 \Rightarrow A = -\frac{19}{425}$, $B = -\frac{8}{425}$

$\boxed{y = C_1 e^{3x/2} + C_2 e^{-x/2} - \frac{19}{425}\cos 2x - \frac{8}{425}\sin 2x}$

---

**Problema 9:** $y'' - y' = -3$

$r^2 - r = 0 \Rightarrow r(r-1) = 0 \Rightarrow r_1 = 0, r_2 = 1$

$y_c = C_1 + C_2 e^x$

$y_p = Ax$ (multiplicar por $x$ porque $r=0$ es raíz)

$y_p' = A$, $y_p'' = 0 \Rightarrow -A = -3 \Rightarrow A = 3$

$\boxed{y = C_1 + C_2 e^x + 3x}$

---

**Problema 10:** $y'' + 2y' = 2x + 5 - e^{-2x}$

$r^2 + 2r = 0 \Rightarrow r(r+2) = 0 \Rightarrow r_1 = 0, r_2 = -2$

$y_c = C_1 + C_2 e^{-2x}$

$y_{p_1} = Ax^2 + Bx$ para $2x + 5$ (multiplicar por $x$)

$y_{p_1}' = 2Ax + B$, $y_{p_1}'' = 2A$

$2A + 2(2Ax + B) = 2x + 5 \Rightarrow 4Ax + 2A + 2B = 2x + 5$

$4A = 2 \Rightarrow A = \frac{1}{2}$, $2A + 2B = 5 \Rightarrow B = 2$

$y_{p_2} = Cxe^{-2x}$ para $-e^{-2x}$ (multiplicar por $x$)

$y_{p_2}' = Ce^{-2x} - 2Cxe^{-2x}$, $y_{p_2}'' = -4Ce^{-2x} + 4Cxe^{-2x}$

$-4Ce^{-2x} + 4Cxe^{-2x} + 2(Ce^{-2x} - 2Cxe^{-2x}) = -e^{-2x}$

$-4C + 2C = -1 \Rightarrow C = \frac{1}{2}$

$\boxed{y = C_1 + C_2 e^{-2x} + \frac{1}{2}x^2 + 2x + \frac{1}{2}xe^{-2x}}$

---

**Problema 11:** $y'' - y' + \frac{1}{4}y = 3 + e^{x/2}$

$r^2 - r + \frac{1}{4} = 0 \Rightarrow \left(r - \frac{1}{2}\right)^2 = 0 \Rightarrow r = \frac{1}{2}$ (doble)

$y_c = C_1 e^{x/2} + C_2 x e^{x/2}$

$y_{p_1} = A \Rightarrow \frac{A}{4} = 3 \Rightarrow A = 12$

$y_{p_2} = Bx^2 e^{x/2}$ para $e^{x/2}$ (multiplicar por $x^2$)

$y_{p_2}' = 2Bxe^{x/2} + \frac{B}{2}x^2 e^{x/2}$

$y_{p_2}'' = 2Be^{x/2} + 2Bxe^{x/2} + Bxe^{x/2} + \frac{B}{4}x^2 e^{x/2} = \left(2B + 3Bx + \frac{B}{4}x^2\right)e^{x/2}$

$\left(2B + 3Bx + \frac{Bx^2}{4}\right) - \left(2Bx + \frac{Bx^2}{2}\right) + \frac{Bx^2}{4} = 1$

$2B = 1 \Rightarrow B = \frac{1}{2}$

$\boxed{y = C_1 e^{x/2} + C_2 x e^{x/2} + 12 + \frac{1}{2}x^2 e^{x/2}}$

---

**Problema 12:** $y'' - 16y = 2e^{4x}$

$r^2 - 16 = 0 \Rightarrow r = \pm 4$

$y_c = C_1 e^{4x} + C_2 e^{-4x}$

$y_p = Axe^{4x}$ (multiplicar por $x$)

$y_p' = Ae^{4x} + 4Axe^{4x}$, $y_p'' = 8Ae^{4x} + 16Axe^{4x}$

$8Ae^{4x} + 16Axe^{4x} - 16Axe^{4x} = 2e^{4x}$

$8A = 2 \Rightarrow A = \frac{1}{4}$

$\boxed{y = C_1 e^{4x} + C_2 e^{-4x} + \frac{1}{4}xe^{4x}}$

---

**Problema 13:** $y'' + 4y = 3\sin 2x$

$r^2 + 4 = 0 \Rightarrow r = \pm 2i$

$y_c = C_1 \cos 2x + C_2 \sin 2x$

$y_p = x(A\cos 2x + B\sin 2x)$ (multiplicar por $x$)

$y_p' = A\cos 2x + B\sin 2x + x(-2A\sin 2x + 2B\cos 2x)$

$y_p'' = -4A\sin 2x + 4B\cos 2x + (-2A\sin 2x + 2B\cos 2x) + x(-4A\cos 2x - 4B\sin 2x)$

$y_p'' = -4A\sin 2x + 4B\cos 2x - 2A\sin 2x + 2B\cos 2x - 4Ax\cos 2x - 4Bx\sin 2x$

$y_p'' + 4y_p = 4B\cos 2x - 4A\sin 2x = 3\sin 2x$

$4B = 0 \Rightarrow B = 0$, $-4A = 3 \Rightarrow A = -\frac{3}{4}$

$\boxed{y = C_1 \cos 2x + C_2 \sin 2x - \frac{3}{4}x\cos 2x}$

---

**Problema 14:** $y'' - 4y = (x^2 - 3)\sin 2x$

$r^2 - 4 = 0 \Rightarrow r = \pm 2$

$y_c = C_1 e^{2x} + C_2 e^{-2x}$

$y_p = (Ax^2 + Bx + C)\cos 2x + (Dx^2 + Ex + F)\sin 2x$

$y_p' = (2Ax + B)\cos 2x - 2(Ax^2 + Bx + C)\sin 2x + (2Dx + E)\sin 2x + 2(Dx^2 + Ex + F)\cos 2x$

$y_p'' = 2A\cos 2x - 4(2Ax + B)\sin 2x - 4(Ax^2 + Bx + C)\cos 2x + 2D\sin 2x + 4(2Dx + E)\cos 2x - 4(Dx^2 + Ex + F)\sin 2x$

$y_p'' - 4y_p = -8(Ax^2 + Bx + C)\cos 2x - 8(Dx^2 + Ex + F)\sin 2x + 2A\cos 2x + 2D\sin 2x - 4(2Ax + B)\sin 2x + 4(2Dx + E)\cos 2x$

$\cos 2x: -8Ax^2 + (8D - 8B)x + (-8C + 2A + 4E) = 0$

$\sin 2x: -8Dx^2 + (-8A - 8E)x + (-8F + 2D - 4B) = x^2 - 3$

$-8D = 1 \Rightarrow D = -\frac{1}{8}$, $-8A - 8E = 0$, $-8F + 2D - 4B = -3$

$-8A = 0 \Rightarrow A = 0$, $8D - 8B = 0 \Rightarrow B = -\frac{1}{8}$, $E = 0$

$-8C + 2A + 4E = 0 \Rightarrow C = 0$

$-8F - \frac{1}{4} + \frac{1}{2} = -3 \Rightarrow F = \frac{13}{32}$

$\boxed{y = C_1 e^{2x} + C_2 e^{-2x} - \frac{1}{8}x\cos 2x + \left(-\frac{1}{8}x^2 + \frac{13}{32}\right)\sin 2x}$

---

**Problema 15:** $y'' + y = 2x\sin x$

$r^2 + 1 = 0 \Rightarrow r = \pm i$

$y_c = C_1 \cos x + C_2 \sin x$

$y_p = x[(Ax + B)\cos x + (Cx + D)\sin x]$ (multiplicar por $x$)

$y_p = (Ax^2 + Bx)\cos x + (Cx^2 + Dx)\sin x$

$y_p' = (2Ax + B)\cos x - (Ax^2 + Bx)\sin x + (2Cx + D)\sin x + (Cx^2 + Dx)\cos x$

$y_p'' = 2A\cos x - (2Ax + B)\sin x - (2Ax + B)\sin x - (Ax^2 + Bx)\cos x + 2C\sin x + (2Cx + D)\cos x + (2Cx + D)\cos x - (Cx^2 + Dx)\sin x$

$y_p'' + y_p = 2A\cos x + 2C\sin x - 2(2Ax + B)\sin x + 2(2Cx + D)\cos x = 2x\sin x$

$\cos x: 2A + 4Cx + 2D = 0 \Rightarrow C = 0, A + D = 0$

$\sin x: 2C - 4Ax - 2B = 2x \Rightarrow A = -\frac{1}{2}, B = 0$

$D = \frac{1}{2}$

$\boxed{y = C_1 \cos x + C_2 \sin x - \frac{1}{2}x^2\cos x + \frac{1}{2}x\sin x}$

---

**Problema 16:** $y'' - 5y' = 2x^3 - 4x^2 - x + 6$

$r^2 - 5r = 0 \Rightarrow r(r-5) = 0 \Rightarrow r_1 = 0, r_2 = 5$

$y_c = C_1 + C_2 e^{5x}$

$y_p = x(Ax^3 + Bx^2 + Cx + D) = Ax^4 + Bx^3 + Cx^2 + Dx$ (multiplicar por $x$)

$y_p' = 4Ax^3 + 3Bx^2 + 2Cx + D$, $y_p'' = 12Ax^2 + 6Bx + 2C$

$12Ax^2 + 6Bx + 2C - 5(4Ax^3 + 3Bx^2 + 2Cx + D) = 2x^3 - 4x^2 - x + 6$

$-20Ax^3 + (12A - 15B)x^2 + (6B - 10C)x + (2C - 5D) = 2x^3 - 4x^2 - x + 6$

$-20A = 2 \Rightarrow A = -\frac{1}{10}$

$12A - 15B = -4 \Rightarrow -\frac{6}{5} - 15B = -4 \Rightarrow B = \frac{14}{75}$

$6B - 10C = -1 \Rightarrow \frac{28}{25} - 10C = -1 \Rightarrow C = \frac{53}{250}$

$2C - 5D = 6 \Rightarrow \frac{53}{125} - 5D = 6 \Rightarrow D = -\frac{697}{625}$

$\boxed{y = C_1 + C_2 e^{5x} - \frac{1}{10}x^4 + \frac{14}{75}x^3 + \frac{53}{250}x^2 - \frac{697}{625}x}$

---

**Problema 17:** $y'' - 2y' + 5y = e^x \cos 2x$

$r^2 - 2r + 5 = 0 \Rightarrow r = \frac{2 \pm \sqrt{4-20}}{2} = 1 \pm 2i$

$y_c = e^x(C_1 \cos 2x + C_2 \sin 2x)$

$y_p = xe^x(A\cos 2x + B\sin 2x)$ (multiplicar por $x$)

$y_p' = e^x(A\cos 2x + B\sin 2x) + xe^x(A\cos 2x + B\sin 2x) + xe^x(-2A\sin 2x + 2B\cos 2x)$

$y_p'' - 2y_p' + 5y_p = e^x(4B\cos 2x - 4A\sin 2x) = e^x\cos 2x$

$4B = 1 \Rightarrow B = \frac{1}{4}$, $-4A = 0 \Rightarrow A = 0$

$\boxed{y = e^x(C_1 \cos 2x + C_2 \sin 2x) + \frac{1}{4}xe^x\sin 2x}$

---

**Problema 18:** $y'' - 2y' + 2y = e^{2x}(\cos x - 3\sin x)$

$r^2 - 2r + 2 = 0 \Rightarrow r = 1 \pm i$

$y_c = e^x(C_1 \cos x + C_2 \sin x)$

$y_p = e^{2x}(A\cos x + B\sin x)$

$y_p' = 2e^{2x}(A\cos x + B\sin x) + e^{2x}(-A\sin x + B\cos x)$

$y_p'' = 4e^{2x}(A\cos x + B\sin x) + 4e^{2x}(-A\sin x + B\cos x) + e^{2x}(-A\cos x - B\sin x)$

$y_p'' - 2y_p' + 2y_p = e^{2x}[(4A + 4B - A - 4A - 2B + 2A)\cos x + (4B - 4A - B - 4B + 2A + 2B)\sin x]$

$= e^{2x}[(A + 2B)\cos x + (-2A + B)\sin x] = e^{2x}(\cos x - 3\sin x)$

$A + 2B = 1$, $-2A + B = -3 \Rightarrow B = 2A - 3$

$A + 4A - 6 = 1 \Rightarrow A = \frac{7}{5}$, $B = \frac{14}{5} - 3 = -\frac{1}{5}$

$\boxed{y = e^x(C_1 \cos x + C_2 \sin x) + e^{2x}\left(\frac{7}{5}\cos x - \frac{1}{5}\sin x\right)}$

---

**Problema 19:** $y'' + 2y' + y = \sec x + 3\cos 2x$

$r^2 + 2r + 1 = 0 \Rightarrow (r+1)^2 = 0 \Rightarrow r = -1$ (doble)

$y_c = C_1 e^{-x} + C_2 xe^{-x}$

$y_{p_1}$ para $\sec x$: usar variación de parámetros (no coeficientes indeterminados)

$y_{p_2} = A\cos 2x + B\sin 2x$ para $3\cos 2x$

$y_{p_2}' = -2A\sin 2x + 2B\cos 2x$, $y_{p_2}'' = -4A\cos 2x - 4B\sin 2x$

$(-4A + 4B + A)\cos 2x + (-4B - 4A + B)\sin 2x = 3\cos 2x$

$-3A + 4B = 3$, $-4A - 3B = 0 \Rightarrow A = -\frac{3B}{4}$

$\frac{9B}{4} + 4B = 3 \Rightarrow B = \frac{12}{25}$, $A = -\frac{9}{25}$

$\boxed{y = C_1 e^{-x} + C_2 xe^{-x} + y_{p_1}(\sec x) - \frac{9}{25}\cos 2x + \frac{12}{25}\sin 2x}$

---

**Problema 20:** $y'' + 2y' - 24y = 16 - (x+2)e^{4x}$

$r^2 + 2r - 24 = 0 \Rightarrow (r+6)(r-4) = 0 \Rightarrow r_1 = -6, r_2 = 4$

$y_c = C_1 e^{-6x} + C_2 e^{4x}$

$y_{p_1} = A \Rightarrow -24A = 16 \Rightarrow A = -\frac{2}{3}$

$y_{p_2} = x(Bx + C)e^{4x}$ para $-(x+2)e^{4x}$ (multiplicar por $x$)

$y_{p_2} = (Bx^2 + Cx)e^{4x}$

$y_{p_2}' = (2Bx + C)e^{4x} + 4(Bx^2 + Cx)e^{4x}$

$y_{p_2}'' = 2Be^{4x} + 8(2Bx + C)e^{4x} + 16(Bx^2 + Cx)e^{4x}$

$y_{p_2}'' + 2y_{p_2}' - 24y_{p_2} = [2B + 16Bx + 8C + 4Bx + 2C]e^{4x} = [20Bx + 2B + 10C]e^{4x}$

$20Bx + 2B + 10C = -x - 2$

$20B = -1 \Rightarrow B = -\frac{1}{20}$, $2B + 10C = -2 \Rightarrow C = -\frac{19}{100}$

$\boxed{y = C_1 e^{-6x} + C_2 e^{4x} - \frac{2}{3} + \left(-\frac{1}{20}x^2 - \frac{19}{100}x\right)e^{4x}}$

---

**Problema 21:** $y''' - 6y'' = 3 - \cos x$

$r^3 - 6r^2 = 0 \Rightarrow r^2(r-6) = 0 \Rightarrow r_1 = 0$ (doble), $r_2 = 6$

$y_c = C_1 + C_2 x + C_3 e^{6x}$

$y_{p_1} = Ax^2$ para $3$ (multiplicar por $x^2$)

$y_{p_1}' = 2Ax$, $y_{p_1}'' = 2A$, $y_{p_1}''' = 0$

$-12A = 3 \Rightarrow A = -\frac{1}{4}$

$y_{p_2} = B\cos x + C\sin x$ para $-\cos x$

$y_{p_2}' = -B\sin x + C\cos x$, $y_{p_2}'' = -B\cos x - C\sin x$, $y_{p_2}''' = B\sin x - C\cos x$

$B\sin x - C\cos x - 6(-B\cos x - C\sin x) = -\cos x$

$(6B - C)\cos x + (B + 6C)\sin x = -\cos x$

$6B - C = -1$, $B + 6C = 0 \Rightarrow B = -6C$

$-36C - C = -1 \Rightarrow C = \frac{1}{37}$, $B = -\frac{6}{37}$

$\boxed{y = C_1 + C_2 x + C_3 e^{6x} - \frac{1}{4}x^2 - \frac{6}{37}\cos x + \frac{1}{37}\sin x}$

---

**Problema 22:** $y''' - 2y'' - 4y' + 8y = 6xe^{2x}$

$r^3 - 2r^2 - 4r + 8 = 0 \Rightarrow (r-2)^2(r+2) = 0 \Rightarrow r_1 = 2$ (doble), $r_2 = -2$

$y_c = C_1 e^{2x} + C_2 xe^{2x} + C_3 e^{-2x}$

$y_p = x^2(Ax + B)e^{2x}$ (multiplicar por $x^2$)

$y_p = (Ax^3 + Bx^2)e^{2x}$

$y_p' = (3Ax^2 + 2Bx)e^{2x} + 2(Ax^3 + Bx^2)e^{2x}$

$y_p'' = (6Ax + 2B)e^{2x} + 4(3Ax^2 + 2Bx)e^{2x} + 4(Ax^3 + Bx^2)e^{2x}$

$y_p''' = 6Ae^{2x} + 12(6Ax + 2B)e^{2x}/6 + ... = e^{2x}[24Ax + 6A + 12B + ...]$

Sustituyendo: $e^{2x}[24Ax + 6A + 12B] = 6xe^{2x}$

$24A = 6 \Rightarrow A = \frac{1}{4}$, $6A + 12B = 0 \Rightarrow B = -\frac{1}{8}$

$\boxed{y = C_1 e^{2x} + C_2 xe^{2x} + C_3 e^{-2x} + \left(\frac{1}{4}x^3 - \frac{1}{8}x^2\right)e^{2x}}$

---

**Problema 23:** $y''' - 3y'' + 3y' - y = x - 4e^x$

$r^3 - 3r^2 + 3r - 1 = 0 \Rightarrow (r-1)^3 = 0 \Rightarrow r = 1$ (triple)

$y_c = C_1 e^x + C_2 xe^x + C_3 x^2 e^x$

$y_{p_1} = Ax + B$ para $x$

$y_{p_1}' = A$, $y_{p_1}'' = 0$, $y_{p_1}''' = 0$

$3A - Ax - B = x \Rightarrow -A = 1, 3A - B = 0$

$A = -1$, $B = -3$

$y_{p_2} = Cx^3 e^x$ para $-4e^x$ (multiplicar por $x^3$)

$y_{p_2}' = 3Cx^2 e^x + Cx^3 e^x$, $y_{p_2}'' = 6Cxe^x + 6Cx^2 e^x + Cx^3 e^x$

$y_{p_2}''' = 6Ce^x + 18Cxe^x + 9Cx^2 e^x + Cx^3 e^x$

$y_{p_2}''' - 3y_{p_2}'' + 3y_{p_2}' - y_{p_2} = 6Ce^x = -4e^x$

$C = -\frac{2}{3}$

$\boxed{y = C_1 e^x + C_2 xe^x + C_3 x^2 e^x - x - 3 - \frac{2}{3}x^3 e^x}$

---

**Problema 24:** $y''' - y'' - 4y' + 4y = 5 - e^x + e^{2x}$

$r^3 - r^2 - 4r + 4 = 0 \Rightarrow (r-1)(r-2)(r+2) = 0 \Rightarrow r_1 = 1, r_2 = 2, r_3 = -2$

$y_c = C_1 e^x + C_2 e^{2x} + C_3 e^{-2x}$

$y_{p_1} = A \Rightarrow 4A = 5 \Rightarrow A = \frac{5}{4}$

$y_{p_2} = Bxe^x$ para $-e^x$ (multiplicar por $x$)

$y_{p_2}' = Be^x + Bxe^x$, $y_{p_2}'' = 2Be^x + Bxe^x$, $y_{p_2}''' = 3Be^x + Bxe^x$

$3Be^x + Bxe^x - 2Be^x - Bxe^x - 4Be^x - 4Bxe^x + 4Bxe^x = -e^x$

$-3B = -1 \Rightarrow B = \frac{1}{3}$

$y_{p_3} = Cxe^{2x}$ para $e^{2x}$ (multiplicar por $x$)

$y_{p_3}' = Ce^{2x} + 2Cxe^{2x}$, $y_{p_3}'' = 4Ce^{2x} + 4Cxe^{2x}$, $y_{p_3}''' = 12Ce^{2x} + 8Cxe^{2x}$

$12C - 4C - 4C + 4C = 1 \Rightarrow 8C - 4C = 1 \Rightarrow C = \frac{1}{4}$

$\boxed{y = C_1 e^x + C_2 e^{2x} + C_3 e^{-2x} + \frac{5}{4} + \frac{1}{3}xe^x + \frac{1}{4}xe^{2x}}$

---

**Problema 25:** $y^{(4)} + 2y'' + y = (x-1)^2$

$r^4 + 2r^2 + 1 = 0 \Rightarrow (r^2 + 1)^2 = 0 \Rightarrow r = \pm i$ (dobles)

$y_c = C_1 \cos x + C_2 \sin x + C_3 x\cos x + C_4 x\sin x$

$y_p = Ax^2 + Bx + C$

$y_p' = 2Ax + B$, $y_p'' = 2A$, $y_p''' = 0$, $y_p^{(4)} = 0$

$4A + Ax^2 + Bx + C = x^2 - 2x + 1$

$A = 1$, $B = -2$, $4A + C = 1 \Rightarrow C = -3$

$\boxed{y = C_1 \cos x + C_2 \sin x + C_3 x\cos x + C_4 x\sin x + x^2 - 2x - 3}$

---

**Problema 26:** $y^{(4)} - y'' = 4x + 2xe^{-x}$

$r^4 - r^2 = 0 \Rightarrow r^2(r^2 - 1) = 0 \Rightarrow r^2(r-1)(r+1) = 0 \Rightarrow r_1 = 0$ (doble), $r_2 = 1, r_3 = -1$

$y_c = C_1 + C_2 x + C_3 e^x + C_4 e^{-x}$

$y_{p_1} = x^2(Ax + B) = Ax^3 + Bx^2$ para $4x$ (multiplicar por $x^2$)

$y_{p_1}' = 3Ax^2 + 2Bx$, $y_{p_1}'' = 6Ax + 2B$, $y_{p_1}''' = 6A$, $y_{p_1}^{(4)} = 0$

$-6Ax - 2B = 4x \Rightarrow A = -\frac{2}{3}$, $B = 0$

$y_{p_2} = x(Cx + D)e^{-x}$ para $2xe^{-x}$ (multiplicar por $x$)

$y_{p_2} = (Cx^2 + Dx)e^{-x}$

$y_{p_2}^{(4)} - y_{p_2}'' = e^{-x}[(-4C)x + (-4D + 4C)] = 2xe^{-x}$

$-4C = 2 \Rightarrow C = -\frac{1}{2}$, $-4D + 4C = 0 \Rightarrow D = -\frac{1}{2}$

$\boxed{y = C_1 + C_2 x + C_3 e^x + C_4 e^{-x} - \frac{2}{3}x^3 + \left(-\frac{1}{2}x^2 - \frac{1}{2}x\right)e^{-x}}$

---

## Problemas con Valores Iniciales (27-31)

---

**Problema 27:** $y'' + 4y = -2$, $y\left(\frac{\pi}{8}\right) = \frac{1}{2}$, $y'\left(\frac{\pi}{8}\right) = 2$

$r^2 + 4 = 0 \Rightarrow r = \pm 2i$

$y_c = C_1 \cos 2x + C_2 \sin 2x$

$y_p = A \Rightarrow 4A = -2 \Rightarrow A = -\frac{1}{2}$

$y = C_1 \cos 2x + C_2 \sin 2x - \frac{1}{2}$

$y' = -2C_1 \sin 2x + 2C_2 \cos 2x$

$y\left(\frac{\pi}{8}\right) = C_1 \cos\frac{\pi}{4} + C_2 \sin\frac{\pi}{4} - \frac{1}{2} = \frac{\sqrt{2}}{2}(C_1 + C_2) - \frac{1}{2} = \frac{1}{2}$

$C_1 + C_2 = \sqrt{2}$

$y'\left(\frac{\pi}{8}\right) = -2C_1 \sin\frac{\pi}{4} + 2C_2 \cos\frac{\pi}{4} = \sqrt{2}(-C_1 + C_2) = 2$

$-C_1 + C_2 = \sqrt{2}$

$2C_2 = 2\sqrt{2} \Rightarrow C_2 = \sqrt{2}$, $C_1 = 0$

$\boxed{y = \sqrt{2}\sin 2x - \frac{1}{2}}$

---

**Problema 28:** $2y'' + 3y' - 2y = 14x^2 - 4x - 11$, $y(0) = 0$, $y'(0) = 0$

$2r^2 + 3r - 2 = 0 \Rightarrow (2r - 1)(r + 2) = 0 \Rightarrow r_1 = \frac{1}{2}, r_2 = -2$

$y_c = C_1 e^{x/2} + C_2 e^{-2x}$

$y_p = Ax^2 + Bx + C$

$y_p' = 2Ax + B$, $y_p'' = 2A$

$4A + 6Ax + 3B - 2Ax^2 - 2Bx - 2C = 14x^2 - 4x - 11$

$-2A = 14 \Rightarrow A = -7$

$6A - 2B = -4 \Rightarrow B = -19$

$4A + 3B - 2C = -11 \Rightarrow -28 - 57 - 2C = -11 \Rightarrow C = -37$

$y = C_1 e^{x/2} + C_2 e^{-2x} - 7x^2 - 19x - 37$

$y(0) = C_1 + C_2 - 37 = 0$

$y' = \frac{1}{2}C_1 e^{x/2} - 2C_2 e^{-2x} - 14x - 19$

$y'(0) = \frac{1}{2}C_1 - 2C_2 - 19 = 0$

$C_1 + C_2 = 37$, $C_1 - 4C_2 = 38$

$5C_2 = -1 \Rightarrow C_2 = -\frac{1}{5}$, $C_1 = \frac{186}{5}$

$\boxed{y = \frac{186}{5}e^{x/2} - \frac{1}{5}e^{-2x} - 7x^2 - 19x - 37}$

---

**Problema 29:** $5y'' + y' = -6x$, $y(0) = 0$, $y'(0) = -10$

$5r^2 + r = 0 \Rightarrow r(5r + 1) = 0 \Rightarrow r_1 = 0, r_2 = -\frac{1}{5}$

$y_c = C_1 + C_2 e^{-x/5}$

$y_p = x(Ax + B) = Ax^2 + Bx$ (multiplicar por $x$)

$y_p' = 2Ax + B$, $y_p'' = 2A$

$10A + 2Ax + B = -6x$

$2A = -6 \Rightarrow A = -3$, $10A + B = 0 \Rightarrow B = 30$

$y = C_1 + C_2 e^{-x/5} - 3x^2 + 30x$

$y(0) = C_1 + C_2 = 0$

$y' = -\frac{1}{5}C_2 e^{-x/5} - 6x + 30$

$y'(0) = -\frac{1}{5}C_2 + 30 = -10 \Rightarrow C_2 = 200$

$C_1 = -200$

$\boxed{y = -200 + 200e^{-x/5} - 3x^2 + 30x}$

---

**Problema 30:** $y'' + 4y' + 4y = (3+x)e^{-2x}$, $y(0) = 2$, $y'(0) = 5$

$r^2 + 4r + 4 = 0 \Rightarrow (r+2)^2 = 0 \Rightarrow r = -2$ (doble)

$y_c = C_1 e^{-2x} + C_2 xe^{-2x}$

$y_p = x^2(Ax + B)e^{-2x}$ (multiplicar por $x^2$)

$y_p = (Ax^3 + Bx^2)e^{-2x}$

$y_p' = (3Ax^2 + 2Bx)e^{-2x} - 2(Ax^3 + Bx^2)e^{-2x}$

$y_p'' = (6Ax + 2B)e^{-2x} - 4(3Ax^2 + 2Bx)e^{-2x} + 4(Ax^3 + Bx^2)e^{-2x}$

$y_p'' + 4y_p' + 4y_p = (6Ax + 2B)e^{-2x} = (3 + x)e^{-2x}$

$6A = 1 \Rightarrow A = \frac{1}{6}$, $2B = 3 \Rightarrow B = \frac{3}{2}$

$y = C_1 e^{-2x} + C_2 xe^{-2x} + \left(\frac{1}{6}x^3 + \frac{3}{2}x^2\right)e^{-2x}$

$y(0) = C_1 = 2$

$y' = -2C_1 e^{-2x} + C_2 e^{-2x} - 2C_2 xe^{-2x} + \left(\frac{1}{2}x^2 + 3x\right)e^{-2x} - 2\left(\frac{1}{6}x^3 + \frac{3}{2}x^2\right)e^{-2x}$

$y'(0) = -2C_1 + C_2 = -4 + C_2 = 5 \Rightarrow C_2 = 9$

$\boxed{y = 2e^{-2x} + 9xe^{-2x} + \left(\frac{1}{6}x^3 + \frac{3}{2}x^2\right)e^{-2x}}$

---

**Problema 31:** $y'' + 4y' + 5y = 35e^{-4x}$, $y(0) = -3$, $y'(0) = 1$

$r^2 + 4r + 5 = 0 \Rightarrow r = \frac{-4 \pm \sqrt{16-20}}{2} = -2 \pm i$

$y_c = e^{-2x}(C_1 \cos x + C_2 \sin x)$

$y_p = Ae^{-4x}$

$y_p' = -4Ae^{-4x}$, $y_p'' = 16Ae^{-4x}$

$16A - 16A + 5A = 35 \Rightarrow 5A = 35 \Rightarrow A = 7$

$y = e^{-2x}(C_1 \cos x + C_2 \sin x) + 7e^{-4x}$

$y(0) = C_1 + 7 = -3 \Rightarrow C_1 = -10$

$y' = -2e^{-2x}(C_1 \cos x + C_2 \sin x) + e^{-2x}(-C_1 \sin x + C_2 \cos x) - 28e^{-4x}$

$y'(0) = -2C_1 + C_2 - 28 = 20 + C_2 - 28 = C_2 - 8 = 1 \Rightarrow C_2 = 9$

$\boxed{y = e^{-2x}(-10\cos x + 9\sin x) + 7e^{-4x}}$

---
